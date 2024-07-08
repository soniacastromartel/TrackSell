<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Target;
use App\Centre;
use App\Employee;
use Auth;
use DB;
use App\Imports\TargetsImport;
use App\Imports\IncentiveImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TargetsExport;
use App\Exports\TracingTargetsExport;
use Illuminate\Support\Facades\Validator;
use App\Services\TargetService;
use Maatwebsite\Excel\Exceptions\SheetNotFoundException;
use DataTables;
use Exception;

class TargetController extends Controller
{

    public $user;
    public $centreId;

    public function __construct()
    {
        $this->user = session()->get('user');
    }

    public function index()
    {
        $this->user = session()->get('user');
        $this->centreId = $this->user['centre_id'];


        $title = 'Calculadora de incentivos';
        $centres = Centre::getCentresActive();
        $employees = Employee::getEmployeesActive();

        return view(
            'calculate_incentives',
            [
                'title'      => $title, 'centres'   => $centres, 'employees' => $employees, 'user'      => $this->user
            ]
        );
    }

    /**
     * Importar Objetivos
     */
    private function importData($request, $onlySales = false)
    {
        try {
            $validator = Validator::make($request->all(), [
                'targetInputFile' => 'max:2048|mimes:xls',
            ]);
            if ($validator->fails()) {
                throw new \Exception('Error, superado tamaño de fichero o formato no excel');
            }
            $fileName = 'targetInputFile';
            //Importar importe de venta privada  - SOLO UN CENTRO
            if ($onlySales === true) {
                $fileName = 'targetInputSalesFile';
                $centres = Centre::where('id', 1)->pluck('id');
                $centres = collect($centres->toArray());
            } else {
                $centres = Centre::getCentresActive();
            }
            $year = isset($request->yearTarget) ? $request->yearTarget : date('Y');
            if ($request->hasFile($fileName)) {
                $request->file($fileName)->move(storage_path(), 'example_target_input.xls');
                Excel::import(new TargetsImport($centres, $year, $onlySales), storage_path() . '/example_target_input.xls');
                Storage::delete(['example_target_input.xls']);
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return redirect('calculateIncentive')->with('error',$e->getMessage());
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        } catch (SheetNotFoundException $e) {
            return redirect('calculateIncentive')->with('error', 'El numero de centros es mayor que el numero de hojas definidos en el documento');
        }
    }

    /**
     * Funcion que se encarga de importar todos los objetivos - Todos los centros
     * 
     */
    public function import(Request $request)
    {
        try {
            if ($request->hasFile('targetInputFile')) {
                $this->importData($request);
                return redirect('calculateIncentive')->with([
                    'title' => 'Calculadora de incentivos', 'success' =>  'Importados objetivos!'
                ]);
            } else {
                return redirect('calculateIncentive')->with('error', 'Error!');
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return redirect('calculateIncentive')->with('error', 'Error de formato de fichero a importar');
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        }
    }

    /** Funcion que se encarga de importar valores de venta privada - Incluido por supervisores */
    public function importSales(Request $request)
    {
        try {
            if ($request->hasFile('targetInputSalesFile')) {
                $this->importData($request, true);
                return redirect('calculateIncentive')->with(
                    [
                        'title' => 'Calculadora de incentivos', 'success' =>  'Importada Venta Privada!'
                    ]
                );
            } else {
                return redirect('calculateIncentive')->with('error', 'Error!');
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return redirect('calculateIncentive')->with('error', 'Error de formato de fichero a importar');
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        }
    }

    /** 
     * Función que obtiene los datos de los incentivos/Calculadora de Incentivos
     * @param Request $request
     */
    public function calculateIncentives(Request $request)
    {
        try {
            $params = $request->all();
            if (!empty($params['centre'])) {
                $centres = Centre::where('name', $params['centre'])->get();
            } else {
                $centres = Centre::getCentresActive();
            }
            $params['centre'] = $centres;

            $targetService = new TargetService();
            $exportData = $targetService->getExportTarget($params);
            return collect($exportData);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Función que exporta los Incentivos/Calculadora de Incentivos
     */
    public function incentivesReportDownload(Request $request)
    {
        try {
            $exportData = $this->calculateIncentives($request);
            $params = $request->all();

            $currentMonth = date('m');
            $year         = date('Y');
            if (isset($params['monthYear']) && !empty($params['monthYear'])) {
                $year         = substr($params['monthYear'], strpos($params['monthYear'], '/') + 1);
                $currentMonth = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
                $previousMonth = $currentMonth - 1;
                $beginYear = $year;
                if ($currentMonth == "1") {
                    $previousMonth = "12";
                    $beginYear -= 1;
                }
                $whereLikeBegin   = $beginYear . '-' . str_pad($previousMonth, 2, "0", STR_PAD_LEFT) . '-' . env('START_DAY_PERIOD');
                $whereLikeLast    = $year . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');
            }

            $filters = [
                'centre'       =>  isset($params['centre'])             ?  $params['centre']             : 'TODOS',
                'employee'     =>  isset($params['employee'])     ?  $params['employee']     : 'TODOS',
                'month'        =>  ltrim($currentMonth, "0"),
                'year'         =>  $year,
                'date_from'    =>  date('d/m/Y', strtotime($whereLikeBegin)), //substr($params['monthYear'], 0,4) 
                'date_to'      =>  date('d/m/Y', strtotime($whereLikeLast)) //substr($params['monthYear'], 0,4) 
            ];

            ob_end_clean();
            ob_start();
            return  Excel::download((new TargetsExport($exportData, $filters)), 'target.xls');
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        }
    }

    /**
     * Función que obtiene los datos de incentivos
     * @param $target
     * @param $centres
     * @param $filters
     */
    private function getCalculatedIncentives($target, $centres, $filters)
    {
        try {
            $targetService = new TargetService();
            $targetDefined = $targetService->getTarget($centres, $filters['month'], $filters['year']);

            if (empty($targetDefined)) {
                $cNames = [];
                foreach ($centres->toArray() as $centre) {
                    $cNames[] = is_array($centre) ? $centre['name'] : $centre->name;
                }
                $centresName = implode(',', $cNames);
                throw new \Exception("Error no se ha definido objetivo para el centro: " . $centresName);
            }

            foreach ($centres as $centre) {
                if (isset($target[$centre->name])) {
                    $totalSupervisors = [];
                    $totalIncomeSuperv = 0;
                    foreach ($target[$centre->name] as $i => $targetRow) {

                        $valueIncentiveObj1 = $this->getDiscount($targetRow, 'service_price_incentive1');
                        $valueIncentiveObj2 = $this->getDiscount($targetRow, 'service_price_incentive2');
                        $totalIncentive = 0;

                        if ($i == 0) {
                            if (!empty($targetDefined)) {

                                $centreData = $centres->filter(function ($centreFind) use ($centre) {
                                    if ($centre->name == $centreFind->name) {
                                        return $centreFind;
                                    }
                                });
                                $params = $filters;
                                unset($params['employee']);
                                $params['monthYear'] = $params['month'] . '/' . $params['year'];
                                $params['centre']    = $centreData;
                                $salesCentres =  $targetService->getExportTarget($params);
                                $targetCentre = $targetService->normalizeData($salesCentres);

                                $targets = [];
                                $targets = $targetService->stateTarget($targetDefined, $filters['month'] . '/' . $filters['year'], $targetCentre, $centreData);
                            }
                        }

                        $result = $targetService->rules($targetRow, $targets, null);

                        $totalIncentive = $this->getDiscount($targetRow, 'service_price_direct_incentive');

                        $isActive = $targetService->employeeActive($targetRow, $params['month'] . '/' . $params['year']);
                        if ($isActive === true) {
                            if ($result['obj1'] === true) {
                                $totalIncentive = $valueIncentiveObj1;
                                if ($result['obj2'] === true) {
                                    $totalIncentive = $valueIncentiveObj2;
                                }
                            }
                        }
                        // Cogemos el grupo de supervisores por centro
                        if ($i == 0) {
                            $totalSuperIncentive  = [];
                        }
                        //REPARTO BONUS HCT - Agrupamos todos los supervisores que haya - Multisupervisor
                        $supervisors = explode(", ", trim($targetRow->supervisor));
                        foreach ($supervisors as $superv) {
                            if (!in_array($superv, $totalSupervisors)) {
                                $totalSupervisors = array_merge($totalSupervisors, [$superv]);
                            }
                        }

                        $totalBonus = 0;
                        //REPARTO BONUS HCT
                        foreach ($supervisors as $supervisorId) {
                            $supervisorId = trim($supervisorId);
                            $supervisor = Employee::find($supervisorId);
                            $isSupervisorActive = $targetService->employeeActive($supervisor, $params['month'] . '/' . $params['year']);
                            if (!isset($totalSuperIncentive[$supervisorId])) {
                                $totalSuperIncentive[$supervisorId] = 0;
                                $totalIncome[$supervisorId] = 0;
                                $totalColIncentive[$supervisorId] = 0;
                            }

                            $valueSuperIncentive1 = $this->getDiscount($targetRow, 'service_price_super_incentive1');
                            $valueSuperIncentive2 = $this->getDiscount($targetRow, 'service_price_super_incentive2');
                            $result = $targetService->rules($targetRow, $targets, array_values($supervisors));
                            $auxIncentive = 0;
                            //Solo aplica bonus de venta, para empleados activos en fecha fin de corte 
                            if ($isActive === true) {
                                if ($result['obj1'] === true) {
                                    $auxIncentive  = $valueSuperIncentive1;
                                    if ($result['obj2'] === true) {
                                        $auxIncentive  = $valueSuperIncentive2;
                                    }
                                }
                            }
                            //Solo aplica bonus de venta, para supervisores activos en fecha fin de corte
                            //REPARTO BONUS HCT
                            if ($isSupervisorActive == false) {
                                $totalSuperIncentive[$supervisorId] = 0;
                            } else {
                                $totalSuperIncentive[$supervisorId] += $auxIncentive * $targetRow->quantity;
                                if (
                                    $centre->id == env('ID_CENTRE_HCT') && $totalBonus == 0
                                    || $centre->id != env('ID_CENTRE_HCT')
                                ) {
                                    if ($supervisorId  == $targetRow->employee_id) {
                                        $totalIncomeSuperv += $totalIncentive * $targetRow->quantity;
                                    }
                                    $totalBonus += $auxIncentive * $targetRow->quantity;
                                }
                            }
                            if ($auxIncentive == 0 && $supervisorId  == $targetRow->employee_id) {
                                $totalIncome[$supervisorId] +=  $totalIncentive * $targetRow->quantity;
                            }
                            $totalColIncentive[$supervisorId] += $totalIncentive * $targetRow->quantity;
                        }
                        /** SUMAMOS BONUS DE VENTA DE TODOS LOS SUPERVISORES DE LA FECHA DEL SEGUIMIENTO */


                        $resultData[] = [
                            'centre_employee' => $targetRow->centre_employee,
                            'patient_name' => $targetRow->patient_name,
                            'service' => $targetRow->service,
                            'hc' => $targetRow->hc,
                            'quantity' => $targetRow->quantity,
                            'employee' => $targetRow->employee,
                            'price' => $this->getDiscount($targetRow, 'price'),
                            'direct_incentive' => $totalIncentive * $targetRow->quantity,
                            'bonus' => $totalBonus,
                            'total_paid' => $totalIncentive * $targetRow->quantity

                        ];
                        /** FILA BONUS SUPERVISOR */
                        //REPARTO BONUS HCT
                        if ($i == count($target[$centre->name]) - 1) {

                            foreach ($totalSupervisors as $supervisorId) {
                                if (!empty($supervisorId)) {
                                    $supervisorId = trim($supervisorId);

                                    $supervisor = Employee::find($supervisorId);

                                    $totalSuperInc    = $totalSuperIncentive[$supervisorId];
                                    $totalSuperIncome = $totalIncome[$supervisorId];
                                    if ($centre->id == env('ID_CENTRE_HCT')) {
                                        $totalSuperInc /=  count($supervisors);
                                    }
                                    $totalSuperIncome += $totalSuperInc;
                                }
                            }
                        }
                    }
                } else {
                    if (!empty($targetDefined)) {

                        $centreData = $centres->filter(function ($centreFind) use ($centre) {
                            if ($centre->name == $centreFind->name) {
                                return $centreFind;
                            }
                        });
                        $params = $filters;
                        unset($params['employee']);
                        $params['monthYear'] = $params['month'] . '/' . $params['year'];
                        $params['centre']    = $centreData;
                        $salesCentres =  $targetService->getExportTarget($params);
                        $targetCentre = $targetService->normalizeData($salesCentres);

                        $targets = [];
                        $targets = $targetService->stateTarget($targetDefined, $filters['month'] . '/' . $filters['year'], $targetCentre, $centreData);
                    }
                }
            }

            return $resultData;
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        }
    }


    /**
     * Función que obtiene los datos del resumen de incentivos
     * @param $target
     * @param $centres
     * @param $filters
     */
    private function getIncentivesSummary($target, $centres, $filters)
    {
        try {
            $targetService = new TargetService();
            $targetDefined = $targetService->getTarget($centres, $filters['month'], $filters['year']);

            $total = [];
            $totalCentre = [];

            foreach ($centres as $centre) {

                $centresData = Centre::where('name', $centre->name)->get();
                $params = $filters;
                unset($params['employee']);
                // $params['monthYear'] = $params['month'] . '/' . $params['year']; 
                $params['centre']    = $centresData;
                $salesCentres =  $targetService->getExportTarget($params);
                $targetCentre = $targetService->normalizeData($salesCentres);

                //$vcTotal =  $targetService->getVC($centresData, $targetCentre); 
                $totalCentre = $targetService->getSummarySales([$centre->name => $target[$centre->name]], $targetDefined, $filters['month'] . '/' . $filters['year'], $centresData, $targetCentre);

                foreach ($totalCentre as $tc => $totalDetail) {
                    $resultData[] = [
                        'centre_name' => $centre->name,
                        'total_incentive' => $totalDetail['total_incentive'],
                        'total_super_incentive' => $totalDetail['total_super_incentive'],
                        'total_income' => $totalDetail['total_income'],

                    ];
                }
            }


            return $resultData;
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        }
    }


    private function getDiscount($targetRow, $field)
    {
        try {
            $result = $targetRow->$field;
            if (!empty($targetRow->discount) && $targetRow->is_calculate === 1) {

                if (strpos($field, 'service_price_') !== false) {
                    $field = substr($field, strlen('service_price_'));
                }

                $fieldDiscount = 'discount_' . $field;
                $result = $targetRow->$fieldDiscount;
            }
            return $result;
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        }
    }

    public function importIncentive(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'targetInputIncentiveFile' => 'max:2048|mimes:xls',
            ]);
            if ($validator->fails()) {
                throw new \Exception('Error, superado tamaño de fichero o formato no excel');
            }
            if ($request->hasFile('targetInputIncentiveFile')) {
                $request->file('targetInputIncentiveFile')->move(storage_path(), 'example_incentive_input.xls');
                Excel::import(new IncentiveImport, storage_path() . '/example_incentive_input.xls');
                Storage::delete(['example_incentive_input.xls']);
                return redirect('/admin/incentives')->with(
                    [
                        'title' => 'Calculadora de incentivos', 'success' =>  'Importados incentivos!'
                    ]
                );
            } else {
                return redirect('/admin/incentives')->with('error', 'Error!');
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return redirect('/admin/incentives')->with('error', 'Error de formato de fichero a importar');
        } catch (\Exception $e) {
            return redirect('/admin/incentives')->with('error', $e->getMessage());
        }
    }


    /** Funcion que obtiene los datos del Seguimiento de Objetivos/Calculadora de Incentivos
     * @param Request $request
     */
    public function tracingTargets(Request $request)
    {
        try {
            $orderIslands = ['HOSPITAL ICOT CIUDAD DE TELDE', 'GRAN CANARIA', 'TENERIFE', 'LANZAROTE', 'FUERTEVENTURA'];

            $params = $request->all();
            $targetService = new TargetService();

            if (!isset($params['type'])) {
                $params['type'] = null;
            }

            if ($params['type'] == 'only') {
                $centres = Centre::getCentreByField($params['centreId']);
            } else {
                $centres = DB::table('centres')
                    ->whereNull('cancellation_date')
                    ->orderByRaw(\DB::raw("FIELD(island, '" . implode('\',\'', $orderIslands) . "' )"))
                    ->get();
            }
            $currentYear = isset($request->yearTarget) ? $request->yearTarget : date('Y');
            $targetData = [];


            $month = 1;
            $params['centre'] = $centres;

            while ($month <= 12) {
                $params['monthYear'] = $month . '/' . $currentYear;
                $target = $targetService->getTarget($centres, $month, $currentYear);
                $exportData = $targetService->getExportTarget($params);

                if (!empty($target)) {
                    $tracking = $targetService->normalizeData($exportData);
                    foreach ($centres as $centre) {
                        if (!isset($targetData[$centre->name])  && isset($target[$centre->id])) {
                            $targetData[$centre->name] = [];
                            $targetData[$centre->name][$currentYear] = [];
                        }

                        $employee_sales = [];
                        $cont_employees = 0;
                        $totalCentre    = [];
                        $vcTotal = 0;
                        if (isset($tracking[$centre->name])) {
                            foreach ($tracking[$centre->name] as $i => $trackingData) {
                                if (!in_array($trackingData->employee_id, $employee_sales)) {
                                    $cont_employees++;
                                    $employee_sales[] = $trackingData->employee_id;
                                }
                            }

                            $centresData = Centre::where('name', $centre->name)->get();
                            $vcTotal =  $targetService->getVC($centresData, $tracking);
                            $totalCentre = $targetService->getSummarySales([$centre->name => $tracking[$centre->name]], $target, $params['monthYear'], $centresData, $tracking);
                        }

                        if (isset($target[$centre->id]) && !empty($target[$centre->id])) {
                            $targetData[$centre->name][$currentYear][$month] = [
                                'obj_vc'        => $target[$centre->id]->obj1,
                                'obj_vp'            => $target[$centre->id]->obj2,
                                'vc'                => $vcTotal,
                                'vp'                => $target[$centre->id]->vd,
                                'cont_employees'    => $cont_employees,
                                'salesPerEmployee'  => $cont_employees > 0 ? $vcTotal / $cont_employees : 0,
                                'total_incentive'   => isset($totalCentre[$centre->name]) ? $totalCentre[$centre->name]['total_income'] : 0,
                                'incentive_percent' => $vcTotal > 0 && isset($totalCentre[$centre->name]) ? $totalCentre[$centre->name]['total_income'] / $vcTotal : 0
                            ];
                            //En table centre se añade exception island - centros independientes como HCT
                            if (!empty($centre->exception_island)) {
                                $centre->island = $centre->exception_island;
                            }
                        }

                        if (!isset($totalTargetByIsland[$centre->island])) {
                            $totalTargetByIsland[$centre->island] = [];
                            $totalTargetByIsland[$centre->island][$currentYear] = [];
                        }
                        if (!isset($totalTargetByIsland[$centre->island][$currentYear][$month])) {
                            $totalTargetByIsland[$centre->island][$currentYear][$month] = [];
                            $totalTargetByIsland[$centre->island][$currentYear][$month] = [
                                'obj_vc'             => 0, 'obj_vp'            => 0, 'vc'                => 0, 'vp'                => 0, 'cont_employees'    => 0, 'salesPerEmployee'  => 0, 'total_incentive'   => 0, 'incentive_percent' => 0
                            ];
                        }
                        if (isset($targetData[$centre->name][$currentYear][$month])) {
                            $totalTargetByIsland[$centre->island][$currentYear][$month]['obj_vc']             +=  $targetData[$centre->name][$currentYear][$month]['obj_vc'];
                            $totalTargetByIsland[$centre->island][$currentYear][$month]['obj_vp']             +=  $targetData[$centre->name][$currentYear][$month]['obj_vp'];
                            $totalTargetByIsland[$centre->island][$currentYear][$month]['vc']                 +=  $targetData[$centre->name][$currentYear][$month]['vc'];
                            $totalTargetByIsland[$centre->island][$currentYear][$month]['vp']                 +=  $targetData[$centre->name][$currentYear][$month]['vp'];
                            $totalTargetByIsland[$centre->island][$currentYear][$month]['cont_employees']     +=  $targetData[$centre->name][$currentYear][$month]['cont_employees'];
                            $totalTargetByIsland[$centre->island][$currentYear][$month]['total_incentive']    +=  $targetData[$centre->name][$currentYear][$month]['total_incentive'];
                        }
                    }
                }
                $month++;
            }

            $totalByIsland = [];

            $totalTargetICOT = [];

            if ($params['type'] != 'only') {

                foreach ($orderIslands as $island) {
                    if (isset($totalTargetByIsland[$island])) {
                        foreach ($totalTargetByIsland[$island] as $year => $totalTargetMonthYear) {
                            foreach ($totalTargetMonthYear as $month => $totalTarget) {
                                if (!isset($totalByIsland[$island])) {
                                    $totalByIsland[$island] = [];
                                    $totalByIsland[$island][$month] = [];
                                }
                                $totalByIsland[$island][$month]['salesPerEmployee']  = $totalTarget['cont_employees'] > 0 ? $totalTarget['vc'] / $totalTarget['cont_employees']  : 0;
                                $totalByIsland[$island][$month]['incentive_percent'] = $totalTarget['vc'] > 0             ? $totalTarget['total_incentive'] / $totalTarget['vc'] : 0;

                                if (!isset($targetData[$island])) {
                                    $targetData[$island] = [];
                                    $targetData[$island][$year] = [];
                                }
                                $targetData[$island][$year][$month] = [];
                                $targetData[$island][$year][$month] = $totalTarget;
                                $targetData[$island][$year][$month]['salesPerEmployee']  = $totalByIsland[$island][$month]['salesPerEmployee'];
                                $targetData[$island][$year][$month]['incentive_percent'] = $totalByIsland[$island][$month]['incentive_percent'];

                                if (!isset($totalTargetICOT[$year])) {
                                    $totalTargetICOT[$year] = [];
                                }
                                if (!isset($totalTargetICOT[$year][$month])) {
                                    $totalTargetICOT[$year][$month] = [
                                        'obj_vc'              => 0, 'obj_vp'            => 0, 'vc'                => 0, 'vp'                => 0, 'cont_employees'    => 0, 'salesPerEmployee'  => 0, 'total_incentive'   => 0, 'incentive_percent' => 0
                                    ];
                                }
                                if ($island != 'HOSPITAL ICOT CIUDAD DE TELDE') {
                                    $totalTargetICOT[$year][$month]['obj_vc']          += $targetData[$island][$year][$month]['obj_vc'];
                                    $totalTargetICOT[$year][$month]['obj_vp']          += $targetData[$island][$year][$month]['obj_vp'];
                                    $totalTargetICOT[$year][$month]['vc']              += $targetData[$island][$year][$month]['vc'];
                                    $totalTargetICOT[$year][$month]['vp']              += $targetData[$island][$year][$month]['vp'];
                                    $totalTargetICOT[$year][$month]['cont_employees']  += $targetData[$island][$year][$month]['cont_employees'];
                                    $totalTargetICOT[$year][$month]['total_incentive'] += $targetData[$island][$year][$month]['total_incentive'];
                                }
                            }
                        }
                    }
                }
            }

            $totalIcot = [];
            if ($params['type'] != 'only') {
                foreach ($totalTargetICOT as $y => $totalYear) {
                    foreach ($totalYear as $month => $totalTargetIcotMonthYear) {
                        $totalIcot[$y][$month] = $totalTargetICOT[$y][$month];
                        $totalIcot[$y][$month]['salesPerEmployee']  =  $totalTargetIcotMonthYear['cont_employees'] > 0 ? $totalTargetIcotMonthYear['vc'] / $totalTargetIcotMonthYear['cont_employees'] : 0;
                        $totalIcot[$y][$month]['incentive_percent'] = $totalTargetIcotMonthYear['vc'] > 0 ? $totalTargetIcotMonthYear['total_incentive'] / $totalTargetIcotMonthYear['vc'] : 0;

                        $targetData['GRUPO ICOT ' . $year][$year][$month] = $totalIcot[$y][$month];
                    }
                }
            }

            unset($targetData['LANZAROTE']);
            unset($targetData['FUERTEVENTURA']);
            unset($targetData['HOSPITAL TELDE']);

            return collect($targetData);
        } catch (\Exception $e) {

            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    /** Funcion que descarga el informe de Seguimiento de objetivos
     * @param request Request
     */
    public function targetsReportDownload(Request $request)
    {
        try {
            $this->user = session()->get('user');
            $centreId = $this->user['centre_id'];
            $employeeId = $this->user['id'];

            if (isset($centreId) && $centreId != null && $employeeId != 525) {
                $request['type'] = 'only';
                $request['centreId'] = $centreId;
            } else {
                $request['type'] = 'full';
            }

            $targetData = $this->tracingTargets($request);
            if (!isset($request->yearTarget)) {

                $request['yearTarget'] = date('Y');
            }
            $data = $request->all();

            ob_end_clean();
            ob_start();
            return  Excel::download((new TracingTargetsExport($targetData, $data)), 'target.xls');
        } catch (\Exception $e) {

            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    /** Funcion que muestra la tabla de Seguimiento de objetivos
     * @param request Request
     */
    public function targetsReportView(Request $request)
    {
        try {
            $params = $request->all();
            $month = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
            $year = substr($params['monthYear'], strpos($params['monthYear'], '/') + 1);
            $request['yearTarget'] = $year;
            $target = $this->tracingTargets($request);



            if (!empty($target)) {
                if (strpos($month, '0') === 0) {
                    $month = substr($month, 1);
                }
                foreach ($target as $t => $key) {
                    if ($params['centre'] == 'HOSPITAL TELDE' && strpos($t, 'HOSPITAL') !== false) {
                        $t = 'HOSPITAL TELDE';
                    }
                    if ($t == $params['centre']) {
                        foreach ($key as $data) {
                            $targetData[] = [
                                'obj_vc' => $data[$month]['obj_vc'],
                                'obj_vp' => $data[$month]['obj_vp'],
                                'vc' => $data[$month]['vc'],
                                'vp' => $data[$month]['vp'],
                                'cont_employees' => $data[$month]['cont_employees'],
                                'salesPerEmployee' => $data[$month]['salesPerEmployee'],

                            ];
                        }
                    }
                }
                if (empty($targetData)) {
                    $targetData = [];
                }
            } else {
                $targetData = [];
            }
            return DataTables::of(collect($targetData))
                ->addIndexColumn()
                ->make(true);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }


    /** Funcion que muestra la tabla de Incentivos
     * @param request Request
     */
    public function incentivesReportView(Request $request)
    {
        try {
            $targetService = new TargetService();
            $params = $request->all();
            $centres = $params['centre'] == null ? Centre::getCentresActive() : Centre::where('name', $params['centre'])->get();
            $currentMonth = date('m');
            $year         = date('Y');
            if (isset($params['monthYear']) && !empty($params['monthYear'])) {
                $year         = substr($params['monthYear'], strpos($params['monthYear'], '/') + 1);
                $currentMonth = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
                $previousMonth = $currentMonth - 1;
                $beginYear = $year;
                if ($currentMonth == "1") {
                    $previousMonth = "12";
                    $beginYear -= 1;
                }
                $whereLikeBegin   = $beginYear . '-' . str_pad($previousMonth, 2, "0", STR_PAD_LEFT) . '-' . env('START_DAY_PERIOD');
                $whereLikeLast    = $year . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');
            }

            $filters = [
                'centre'       =>  isset($params['centre'])             ?  $params['centre']             : 'TODOS',
                'employee'     =>  isset($params['employee'])     ?  $params['employee']     : 'TODOS',
                'month'        =>  ltrim($currentMonth, "0"),
                'year'         =>  $year,
                'date_from'    =>  date('d/m/Y', strtotime($whereLikeBegin)), //substr($params['monthYear'], 0,4) 
                'date_to'      =>  date('d/m/Y', strtotime($whereLikeLast)) //substr($params['monthYear'], 0,4) 
            ];


            $targetData = $this->calculateIncentives($request);

            $resultData = $targetService->normalizeData($targetData);
            if (!empty($resultData)) {
                $incentivesData = $this->getCalculatedIncentives($resultData, $centres, $filters);
            } else {
                $incentivesData = [];
            }

            return DataTables::of(collect($incentivesData))
                ->addIndexColumn()
                ->make(true);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /** Funcion que muestra la tabla de Resumen de Incentivos
     * @param request Request
     */
    public function incentivesSummaryView(Request $request)
    {
        try {
            $targetService = new TargetService();
            $params = $request->all();
            // $centres = $params['centre'] == null ? Centre::getCentresActive() : Centre::where('name', $params['centre'])->get();
            $currentMonth = date('m');
            $year         = date('Y');

            if (isset($params['monthYear']) && !empty($params['monthYear'])) {
                $year         = substr($params['monthYear'], strpos($params['monthYear'], '/') + 1);
                $currentMonth = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
                $previousMonth = $currentMonth - 1;
                $beginYear = $year;
                if ($currentMonth == "1") {
                    $previousMonth = "12";
                    $beginYear -= 1;
                }
                $whereLikeBegin   = $beginYear . '-' . str_pad($previousMonth, 2, "0", STR_PAD_LEFT) . '-' . env('START_DAY_PERIOD');
                $whereLikeLast    = $year . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');
            }

            $targetData = $this->calculateIncentives($request);
            $resultData = $targetService->normalizeData($targetData);
            if (!empty($resultData)) {
                $centres = Centre::whereIn('name', array_keys($resultData))->get();
                $filters = [
                    //'centre'       =>  isset($params['centre'])             ?  $params['centre']             : 'TODOS',
                    'employee'     =>  isset($params['employee'])     ?  $params['employee']     : 'TODOS',
                    'monthYear'     =>  $params['monthYear'],
                    'month'        =>  ltrim($currentMonth, "0"),
                    'year'         =>  $year,
                    'date_from'    =>  date('d/m/Y', strtotime($whereLikeBegin)), //substr($params['monthYear'], 0,4) 
                    'date_to'      =>  date('d/m/Y', strtotime($whereLikeLast)) //substr($params['monthYear'], 0,4) 
                ];

                $summaryData = $this->getIncentivesSummary($resultData, $centres, $filters);
            } else {
                $summaryData = [];
            }

            return DataTables::of(collect($summaryData))
                ->addIndexColumn()
                ->make(true);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }
}
