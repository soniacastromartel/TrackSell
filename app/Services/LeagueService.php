<?php


namespace App\Services;

use Illuminate\Http\Request;
use Exception;
use App\Centre;
use Illuminate\Support\Facades\DB;
use App\Services\TargetService;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de datos para la liga de centros
 */

class LeagueService
{

    public function generateLeague(Request $request)
    {
        try {
            $currentYear = date('Y');
            $currentMonth = (int) date('m'); // Ensure currentMonth is an integer
            $isCurrentYear = false;

            // Retrieve league data
            $annualCentreData = $this->getLeagueData($request);
            $requestParams = $request->all();

            if ($requestParams['year'] == $currentYear) {
                $isCurrentYear = true;
            }

            if (!empty($annualCentreData[0])) {
                $classificationData = [];

                foreach ($annualCentreData as $centreIndex => $yearlyData) {
                    $totalPoints = 0;
                    $totalCvSum = 0;

                    foreach ($yearlyData as $monthIndex => $monthlyData) {
                        // Accumulate points and CV based on conditions
                        if (is_null($requestParams['month'])) {
                            $totalPoints += $monthlyData->points;
                            $monthlyData->cv = ($monthlyData->cv === -2) ? 0 : $monthlyData->cv;
                            $totalCvSum += $monthlyData->cv;

                            if ($isCurrentYear && $monthIndex == $currentMonth - 1) {
                                break; // Stop at the current month's data
                            }
                        } elseif ($monthlyData->month == $requestParams['month']) {
                            $totalPoints = $monthlyData->points;
                            $totalCvSum = $monthlyData->cv;
                        }
                    }

                    // Calculate average CV
                    $monthsCount = $isCurrentYear ? $currentMonth : 12;
                    if (is_null($requestParams['month'])) {
                        $totalCvSum /= $monthsCount;
                    }

                    $totalCvSum = max($totalCvSum, -1);
                    $centreDetails = Centre::getCentreByField($yearlyData[0]->centre_id);
                    $classificationData[$centreIndex] = [
                        'centre' => $centreDetails[0]->name,
                        'points' => $totalPoints,
                        'average' => floor($totalCvSum) == $totalCvSum ? $totalCvSum : number_format($totalCvSum, 3)
                    ];
                }

                // Sort and add positions to classification data
                $sortedClassification = $this->sortAndRankClassification($classificationData);
                return collect($sortedClassification);
            }

            return [];
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Sorts the classification data by points and CV, then ranks each entry.
     *
     * @param array $classificationData
     * @return array
     */
    private function sortAndRankClassification(array $classificationData): array
    {
        $pointsColumn = array_column($classificationData, 'points');
        $cvColumn = array_column($classificationData, 'average');

        array_multisort($pointsColumn, SORT_DESC, $cvColumn, SORT_DESC, $classificationData);

        foreach ($classificationData as $index => &$entry) {
            $entry['position'] = $index + 1;
        }

        return $classificationData;
    }


    private function getLeagueData(Request $request)
    {
        try {
            $params = $this->prepareParams($request);
            $targetService = new TargetService();
            // Obtención de objetivos definidos
            $targetsDefined = $this->fetchTargets($params);
            // Normalización de datos de seguimiento
            $exportData = $targetService->getExportTarget($params);
            $trackingData = $targetService->normalizeData($exportData);
            // Validación de consulta individual
            if (!empty($params['specificCentre']) && empty($trackingData[$params['specificCentre']])) {
                return [];
            }
            // Procesamiento de datos por centro y mes
            $processedData = $this->processTargetsByCentre($params['centres'], $targetsDefined, $trackingData, $targetService, $params['year']);
            // Cálculo del coeficiente de venta y retorno de datos
            return $this->calculateAndSetExtraPoints($processedData, $params['year']);
        } catch (Exception $ex) {
            Log::debug($ex);
            return null;
        }
    }
    
    /**
     * Prepara los parámetros para la consulta.
     */
    private function prepareParams(Request $request): array
    {
        $params = $request->all();
        $params['acumulative'] = true;
        $params['monthYear'] = '1/' . $params['year'];
        $params['centres'] = Centre::getCentersWithoutHCT();
        $params['specificCentre'] = $request['centre'] ?? null;
        return $params;
    }
    
    /**
     * Construye la consulta y obtiene los objetivos definidos.
     */
    private function fetchTargets(array $params)
    {
        $idCentres = array_column($params['centres']->toArray(), 'id');
        $whereFields = "centre_id IN (" . implode(",", $idCentres) . ") ";
        $whereFields .= "AND ((month BETWEEN 1 AND 12) AND year BETWEEN {$params['year']} AND {$params['year']})";
        return DB::table('targets')
            ->whereRaw($whereFields)
            ->orderBy('month', 'asc')
            ->orderBy('year', 'asc')
            ->get()
            ->toArray();
    }
    
    /**
 * Procesa los objetivos por centro y mes, calculando los VC por mes.
 */
private function processTargetsByCentre($centres, $targets, $trackingData, $targetService, $year)
{
    $processedData = [];
    foreach ($centres as $centre) {
        $centreTargets = array_filter($targets, function ($target) use ($centre) {
            return $target->centre_id == $centre->id;
        });
        foreach ($centreTargets as $target) {
            if (isset($trackingData[$centre->name])) {
                $filteredTracking = $targetService->filterTarget($trackingData[$centre->name], $target->month, $year);
                $vcMonth = $targetService->getVC($centres, [$centre->name => $filteredTracking]);
                $target->vc = $vcMonth;
            } else {
                $target->vc = 0;
            }
            $processedData[$centre->name][] = $target;
        }
    }
    return $processedData;
}

    
    /**
     * Calcula coeficientes de venta y asigna puntos extra.
     */
    private function calculateAndSetExtraPoints(array $processedData, int $year)
    {
        $finalData = [];
        foreach ($processedData as $centreName => $centreData) {
            foreach ($centreData as $target) {
                $centre = Centre::getCentreByField($target->centre_id);
                $monthlyTarget = (new TargetService())->getTarget($centre, $target->month, $year);
    
                if ($target->month != $monthlyTarget[$centre[0]->id]->month) {
                    $target->obj1 = $monthlyTarget[$centre[0]->id]->obj1;
                    $target->obj2 = $monthlyTarget[$centre[0]->id]->obj2;
                }
            }
            $finalData[] = $this->calculations($centreData, $year);
        }
        return $this->setExtraPoint($finalData);
    }
    

    /**
     * @params $request Solicitud de datos
     * Detalle de puntuacion obtenida para un centro en concreto
     * 
     * @return Datatable of data
     */
    public function detailsCentreLeague(Request $request)
    {
        $tPoints = 0;
        try {
            $centre = Centre::getCentreByField($request['centre']);
            $dataRes = $this->getLeagueData($request);

            $months = env('MONTHS_VALUES');
            $monthsTrim = explode(',', $months);

            if (!empty($dataRes[0])) {
                $finalDataRes = [];
                foreach ($dataRes as $data) {
                    if ($data[0]->centre_id == $centre[0]->id) {
                        $finalDataRes = $data;
                        foreach ($data as $i => $month) {
                            if ($data[$i]->cv === -2) {
                                $data[$i]->cv = 0;
                            }
                            $tPoints += $data[$i]->points;
                            $finalDataRes[$i]->month = ['id' => $i, 'month' => $monthsTrim[$i]];
                        }
                        break;
                    }
                }
                return $finalDataRes;
            } else {
                return [];
            }
        } catch (Exception $ex) {
            return response()->json(
                [
                    'success' => 'false',
                    'errors' => $ex->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Realiza los calculos de coeficiente de venta para 
     * cada grupo.
     * Se aplica la siguiente formula:
     *  (Venta Privada [vd] - Objetivo Venta Privada) [obj2] + 
     *  (Venta Cruzada [vc] - Objetivo Venta Cruzada) [obj1] /
     *  (Objetivo Venta Cruzada [obj1] + Objetivo Venta Privada [obj2]) )
     * 
     * @param $dataGroup: Grupo de valores.
     **/
    public function calculations(array $dataGroup, int $year)
    {
        $dataCalculate = [];

        foreach ($dataGroup as $i => $dg) {
            if (($dg->year == date('Y') && $dg->month <= date('n')) || ($dg->year < date('Y'))) {
                if ($dg->vc >= 0 || $dg->vd >= 0) {
                    $coeficienteVenta = (($dg->vd - $dg->obj2) + ($dg->vc - $dg->obj1)) / ($dg->obj1 + $dg->obj2);
                    $dataCalculate[] = $dataGroup[$i];
                    $dataCalculate[$i]->cv = round($coeficienteVenta, 4, PHP_ROUND_HALF_UP);
                    $dataCalculate[$i]->points = $this->setPointsForTargets($dg);
                } else {
                    $dataCalculate[] = $dataGroup[$i];
                    $dataCalculate[$i]->cv = -1;
                    $dataCalculate[$i]->points = $this->setPointsForTargets($dg);
                }
            } else {
                $dataCalculate[] = $dataGroup[$i];
                $dataCalculate[$i]->cv = 0;
                $dataCalculate[$i]->points = $this->setPointsForTargets($dg);
            }
        }
        return $dataCalculate;
    }

    /**
     * Establece los puntos según sus resultados:
     * 
     * Suma 1 punto si la venta cruzada supera al objetivo de venta cruzada
     * Suma 1 más si tambien supera la venta privada al objetivo de venta privada
     * 
     * @return points Puntos conseguidos por cumplir objs
     */
    public function setPointsForTargets($dataCentre)
    {
        $points = 0;
        // Si se cumple objetivo venta cruzada
        if ($dataCentre->vc > $dataCentre->obj1) {
            $points++;
            // Si tambien se cumple objetivo venta privada
            if ($dataCentre->vd > $dataCentre->obj2) {
                $points++;
            }
        }
        return $points;
    }

    /**
     * Extrae (por mes) el centro con mayor coeficiente de venta y
     * establece un punto extra al ganador mensual
     * 
     * @return finalCollection Coleccion calculada
     */
    public function setExtraPoint(array $centreData): array
    {
        $monthlyWinners = [];
        // Find the centre with the highest CV for each month
        for ($month = 0; $month < 12; $month++) {
            $highestCV = 0;
            $winningCentreId = -1;

            foreach ($centreData as $centre) {
                if (!empty($centre) && isset($centre[$month])) {
                    $currentCV = round($centre[$month]->cv, 3);
                    if ($currentCV > round($highestCV, 3)) {
                        $highestCV = $currentCV;
                        $winningCentreId = $centre[$month]->centre_id;
                    }
                }
            }
            $monthlyWinners[] = [
                'month' => $month + 1,
                'centre_id' => $winningCentreId,
                'cv' => $highestCV,
            ];
        }
        // Assign extra points to the winning centres
        foreach ($centreData as $centreIndex => $centreYearData) {
            foreach ($centreYearData as $monthIndex => $data) {
                foreach ($monthlyWinners as $winner) {
                    if ($data->month == $winner['month'] && $data->centre_id == $winner['centre_id']) {
                        $centreData[$centreIndex][$monthIndex]->points++;
                        $centreData[$centreIndex][$monthIndex]->extra = [
                            'month' => $winner['month'],
                            'cv' => $winner['cv'],
                        ];
                    }
                }
            }
        }
        return $centreData;
    }

}