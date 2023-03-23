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

class LeagueService {

    /**
     * Genera la liga de centros
     * @param request Solicitud recibida
     */
    public function generateLeague(Request $request)
    {
        try{
            $finalRes = $this->getLeagueData($request);
            $params = $request->all();


            if (!empty($finalRes[0])) {
                $clasification = ['data' => []];
                $totalPoints = 0;
                $cvSum = 0;
                foreach ($finalRes as $i=>$fr) {
                    $totalPoints = 0;
                    $cvSum = 0;
                    
                    foreach ($fr as $data) {
                        if ($params['month'] == null) {
                            $totalPoints += $data->points;
                            if ($data->cv === -2) {
                                $data->cv = 0;
                            }
                           
                            $cvSum += $data->cv;
                        } else {
                            if ($data->month == $params['month']) {
                                $totalPoints = $data->points;
                                $cvSum = $data->cv;
                            }
                        }
                    }

                    if($cvSum < -1){
                        $cvSum= -1;
                    }
                    
                    $actualCentre = Centre::getCentreByField($fr[0]->centre_id);
                    $clasification['data'][$i] = [
                        'centre'=>$actualCentre[0]->name,
                        'points'=>$totalPoints,
                        'average'=> round($cvSum, 2)
                    ];
                }

                $finalClasification = [];
                $orderedPoints = array_column($clasification['data'], 'points');
                $orderedCv = array_column($clasification['data'], 'average');
                array_multisort($orderedPoints, SORT_DESC, $orderedCv, SORT_DESC, $clasification['data']);
                foreach ($clasification['data'] as $index=>$clasif) {
                    $clasif['position'] = $index+1;
                    $finalClasification[] = $clasif;
                }
                return collect($finalClasification);
            } else {
                return [];
            }
        } catch (Exception $e) {            
            return response()->json(
                [
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ], 400 ); 
        }
    }

    /**
     * Realiza los cálculos generales para generar la clasificacion
     */
    private function getLeagueData(Request $request)
    {
        try{
            // Tratamiento y construccion de consulta
            $params = $request->all();
            $targetService = new TargetService();

            $year  = $params['year'];
            $params['acumulative'] = true; 
            $params['monthYear'] = '1/'. $params['year']; 

            $centres = Centre::getCentersWithoutHCT();
            $params['centre'] = $centres;           

            $whereFields = "";          
            $idCentres = array_column($params['centre']->toArray(), 'id');

            $whereFields .=  " centre_id in (" . implode(",", $idCentres) . ")" ;
            $whereFields .=  " and ((month between 1 and 12)  and  year between " .  $year . ' and ' . $year .')';

            // Obtencion de datos segun parametros
            $targetsDefined = DB::table('targets')
                ->whereRaw($whereFields)
                ->orderBy('month', 'asc')
                ->orderBy('year', 'asc')
                ->get()
                ->toArray();

            $exportData = $targetService->getExportTarget($params);
            $tracking = $targetService->normalizeData($exportData);

            // Si es una consulta individual y en caso de no existir 
            // trackings en centro se sale
            if (!empty($request['centre']) && empty($tracking[$request['centre']])) {
                return []; 
            }

            $regData = []; 
            $groupYearCentreMonth = [];               
            $size = count($targetsDefined);
            foreach ( $centres as $c ) {
                for ($i=0; $i < $size; $i++) {  
                    if ($c->id == $targetsDefined[$i]->centre_id ) {
                        $regData[] = $targetsDefined[$i];
                    }                  
                }  
            }

            foreach ( $params['centre'] as $centre ) {
                foreach ( $regData as $reg ) {
                    if ($reg->centre_id == $centre->id) {
                        if (isset($tracking[$centre->name])) {
                            $trackingsC = $targetService->filterTarget($tracking[$centre->name], $reg->month, $year);
                            $vcMonth = $targetService->getVC($params['centre'], [$centre->name => $trackingsC]);
                            $reg->vc = $vcMonth;                            
                            $groupYearCentreMonth[] = $reg;
                        } else {
                            $reg->vc = 0;
                            $groupYearCentreMonth[] = $reg;
                        }                                       
                    }
                }

                $yearGroupCentre[$centre->name] = $groupYearCentreMonth;
                $groupYearCentreMonth = [];                      
            }

            // Redefine el obj real para los meses donde no se ha cumplido objetivo
            foreach ( $yearGroupCentre as $center4Months) { 
                $c4m = $center4Months;
                foreach ( $center4Months as $centreMonth ) { 
                    $centreSearch = Centre::getCentreByField($centreMonth->centre_id);
                    $objCumplir = $targetService->getTarget($centreSearch, $centreMonth->month, $year);

                    if ($centreMonth->month != $objCumplir[$centreSearch[0]->id]->month) {
                        $c4m[$centreMonth->month-1]->obj1 = $objCumplir[$centreSearch[0]->id]->obj1;
                        $c4m[$centreMonth->month-1]->obj2 = $objCumplir[$centreSearch[0]->id]->obj2;
                    }
                }
                $calcCoeficienteVenta[] = $this->calculations($c4m, $params['year']);
            }
            // Se suman los puntos extras y se retornan los datos agrupados y calculados
            return $this->setExtraPoint($calcCoeficienteVenta);
        } catch(Exception $ex){
            Log::debug($ex);
            return null;
        }
    }

    /**
     * @params $request Solicitud de datos
     * Detalle de puntuacion obtenida para un centro en concreto
     * 
     * @return Datatable of data
     */
    public function detailsCentreLeague(Request $request )
    {
        $tPoints = 0;
        try{
            $centre = Centre::getCentreByField($request['centre']);
            $dataRes = $this->getLeagueData($request);

            $months = env('MONTHS_VALUES');
            $monthsTrim = explode(',', $months);

            if (!empty($dataRes[0]) ) {
                $finalDataRes = [];
                foreach ($dataRes as $data) {
                    if ($data[0]->centre_id == $centre[0]->id) {
                        $finalDataRes = $data;
                        foreach ($data as $i=>$month) {
                            if ($data[$i]->cv === -2) {
                                $data[$i]->cv = 0;
                            }
                            $tPoints += $data[$i]->points;
                            $finalDataRes[$i]->month = ['id'=>$i, 'month'=>$monthsTrim[$i]];
                        }
                        break;
                    }
                }
                return $finalDataRes;
            } else {
                return [];
            }
        } catch(Exception $ex){
            return response()->json(
                [
                'success' => 'false',
                'errors'  => $ex->getMessage(),
            ], 400 );
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
    public function calculations(Array $dataGroup, int $year)
    {        
        $dataCalculate = [];
        
        foreach ($dataGroup as $i=>$dg) {
            if (($dg->year == date('Y') && $dg->month <= date('n')) || ($dg->year < date('Y'))) {
                if ($dg->vc >= 0 || $dg->vd >=0){
                    $coeficienteVenta = (($dg->vd - $dg->obj2) + ($dg->vc - $dg->obj1)) / ($dg->obj1 + $dg->obj2);
                    $dataCalculate[] = $dataGroup[$i];
                    $dataCalculate[$i]->cv = round($coeficienteVenta, 2, PHP_ROUND_HALF_UP);
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
    public function setExtraPoint(Array $collection )
    {
        $winnerDataForCentre = [];

        for ($month=0; $month<12; $month++) {
            $calcCVBig = 0;
            $centreWinner = -1;
            for ( $i=0; $i<count($collection); $i++ ) {
                if (count($collection[$i]) > 0 ) {
                    if (round($collection[$i][$month]->cv, 2) > round($calcCVBig, 2)) {
                        $calcCVBig = $collection[$i][$month]->cv;
                        $centreWinner = $collection[$i][$month]->centre_id;
                    }
                }
            }
            $winnerDataForCentre[] = ['month'=>$month+1, 'centre' => $centreWinner, 'cv'=>$calcCVBig];
        }   

        $finalCollection = $collection;
        foreach ($collection as $i=>$reg) {
            foreach ($winnerDataForCentre as $wdc) {
                foreach ($reg as $pos=>$centreMonthData) {
                    if ($wdc['month'] == $centreMonthData->month && $wdc['centre'] == $centreMonthData->centre_id) {
                        $finalCollection[$i][$pos]->points++;
                        $finalCollection[$i][$pos]->extra = ['month' => $wdc['month'], 'cv' => $wdc['cv'] ];
                    }
                }
            }
        }
        return $finalCollection;
    }
}