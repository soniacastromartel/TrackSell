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
            return redirect('calculateIncentive')->with('error', 'Error de formato de fichero a importar');
        } catch (\Exception $e) {
            return redirect('calculateIncentive')->with('error', $e->getMessage());
        } catch (SheetNotFoundException $e) {
            return redirect('calculateIncentive')->with('error', 'El numero de centros es mayor que el numero de hojas definidos en el documento');
        }
    }

    /** Funcion que se encarga de importar todos los objetivos - Todos los centros */
    //FIXME: ESTE METODO NO SE PUEDE REFACTORIZAR CON IMPORT SALES??
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

    public function calculateTargets(Request $request)
    {
        try {
            $params = $request->all();
            $filters = [];

            $params = $request->all();
            $centreName = $params['centre'];
            if (!empty($params['centre'])) {
                $centres = Centre::where('name', $params['centre'])->get();
            } else {
                $centres = Centre::getCentresActive();
            }
            $params['centre'] = $centres;

            $targetService = new TargetService();
            $exportData = $targetService->getExportTarget($params);
            $currentMonth = date('Y');
            $year         = date('m');
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
                'centre'       =>  isset($centreName)             ?  $centreName             : 'TODOS',
                'employee'     =>  isset($params['employee'])     ?  $params['employee']     : 'TODOS',
                'month'        =>  ltrim($currentMonth, "0"),
                'year'         =>  $year,
                'date_from'    =>  date('d/m/Y', strtotime($whereLikeBegin)), //substr($params['monthYear'], 0,4) 
                'date_to'      =>  date('d/m/Y', strtotime($whereLikeLast)) //substr($params['monthYear'], 0,4) 
            ];

            //TODO...
            // Comprobar regla de supervisor
            // Obtener primer obj1 - no cumplido 
            ob_end_clean();
            ob_start();
            return  Excel::download((new TargetsExport($exportData, $filters)), 'target.xls');
        } catch (\Exception $e) {
            //FIXME.... response error text alert doesn't works 
            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ], 400);
            //Redirect::to('/calculateIncentive')->with('error', $e->getMessage());
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

            $params= $request -> all();
            $targetService = new TargetService();
            if ($params['type']=='only') {
                $centres = Centre:: getCentreByField( $params['centreId']);
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

            if($params['type']!='only'){

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
            if ($params['type']!='only'){
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

            ob_end_clean();
            ob_start();
            // return  Excel::download((new TracingTargetsExport(collect($targetData), ['year' => $currentYear ])),'target.xls');

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

            if (isset($centreId) && $centreId != null) {
                $request['type']='only';
                $request ['centreId']= $centreId;
            }else {
             $request['type']='full';
            }

            $targetData = $this->tracingTargets($request);
            if (!isset($request->yearTarget)){ 
           
                $request ['yearTarget'] = date('Y');
            }  
            $data =$request -> all();
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
            $targetData = $this->tracingTargets($request);
            $target = collect($targetData);
            foreach ($target as $t) {
                $t = $t;
                $year = $t['2022'];
                foreach ($year as $a) {
                    $objVC = $a['obj_vc'];
                    $objVP = $a['obj_vp'];
                    $vc = $a['vc'];
                    $vp = $a['vp'];
                    $contEmployees = $a['cont_employees'];
                    $salesPerEmployee = $a['salesPerEmployee'];
                    $totalIncentive = $a['total_incentive'];
                    $incentivePercent = $a['incentive_percent'];
                }
            }
            // return DataTables::of(collect($targetData))
            //     ->addIndexColumn()
            //     ->make(true);
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
