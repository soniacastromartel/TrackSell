<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Employee;
use App\Centre;
use App\Service;
use App\Tracking;
use DB;
use App\Services\TargetService;
use Exception;
use Illuminate\Support\Facades\Validator;

class TrackingController extends BaseController {

    /* MIS INCENTIVOS */
    public function incentives(Request $request) {

        try {

            /**
             *
             * Parametros: 
             * @username nombre de usuario
             * @request-> month / @request-> year
             * Posibles casos, según parametros recibidos
             * Incentivo solo con month - año en curso
             * Incentivo solo con year  - acumulativo
             * Incentivo con month && year
             */

            $params       = $request->all();
            $beginYear    = date('Y');
            $currentMonth = ltrim(date('m'), '0');
            $acumulative  = false;
            if (isset($params['year'])) {
                $beginYear = $params['year'];
                if (isset($params['month'])) {
                    $currentMonth = ltrim($params['month'], '0'); // AÑO Y MES PASADOS
                } else {
                    $currentMonth = null;  //TODO EL AÑO
                    $acumulative  = true;
                }
            } elseif (isset($params['month'])) {
                $currentMonth = ltrim($params['month'], '0'); // MES PASADO Y AÑO EN CURSO
            }

            $username = $params['username'];
            $employee = Employee::where('username', $username)->first();
            if (empty($employee)) {
                return $this->sendError('Error, empleado con nombre de usuario ' . $username . ' no encontrado', 500);
            }

            $params = [];
            $params['month']     =  $currentMonth;
            $params['year']      =  $beginYear;
            $params['employee']  =  $employee->name;
            $params['username']  =  $request->username;

            /*Datos devueltos:
            * contServicios   - numero de servicios
            * totalIncentivos - total de incentivos
            * totalVenta      - total importe de servicios vendidos
            * listaServicios  -  [ nombre servicio , fecha estado, incentivo, estado ]
            * */
            $success = $this->getDataIncentives($params, $acumulative);

            return $this->sendResponse($success, '');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500);
        }
    }

    /**
     * Function to get data of incentives
     * @params: 
     *  params:
     *  month
     *  year
     *  employee
     *  acumulative - when true get data from all year 
     */
    private function getDataIncentives($params, $acumulative) {
        $targetService = new TargetService();
        $monthTo = date('m');
        if ($acumulative) {
            $month = 1;
        } else {
            $month = $params['month'];
            $monthTo = $month;
        }
        $employee = $params['employee'];  //Save a copy, lose when unset later
        $totalDefIncentive = 0;
        $totalDefSale = 0;
        $detailSale = [];
        $contSales = 0;
        while ($month <= $monthTo) {
            $params['monthYear'] =  $month . '/' . $params['year'];
            $params['employee']  = $employee;
            $params['todos_estados'] = true;
            $params['centre'] = null;
            $employeeSales = $targetService->getExportTarget($params);
            if (empty($employeeSales->toArray())) {
                $month++;
                continue;
            }
            $idCentres     = array_unique(array_column($employeeSales->toArray(), 'centre_employee_id'));
            $centres       = Centre::whereIn('id', $idCentres)->get();

            unset($params['employee']);
            $params['centre'] = $centres;
            $params['todos_estados'] = false;
            $salesCentres =  $targetService->getExportTarget($params);
            $target = $targetService->normalizeData($salesCentres);

            $targetDefined = $targetService->getTarget($centres,  $month, $params['year']);
            if (empty($targetDefined) && !empty($centres)) {
                return $this->sendError('Error no se ha definido objetivo para el centro:' . implode(',', array_unique(array_keys($target))), 500);
            }


            $targets = [];
            foreach ($employeeSales as $key => $sale) {

                foreach ($centres as $cId => $centre) {
                    if ($centre->id == $sale->centre_employee_id) {

                        if (empty($targets)) {
                            $centreData = $centres->filter(function ($centreFind) use ($centre) {
                                if ($centreFind->id  == $centre->id) {
                                    return $centreFind;
                                }
                            });
                            $targets = $targetService->stateTarget($targetDefined, $month . '/' . $params['year'], $target, $centreData);
                        }

                        $valueIncentiveObj1   = isset($sale->service_price_incentive1) ? $sale->service_price_incentive1 : $sale->obj1_incentive;
                        $valueIncentiveObj2   = isset($sale->service_price_incentive2) ? $sale->service_price_incentive2 : $sale->obj2_incentive;
                        $totalIncentive = 0;
                        $result = $targetService->rules($sale, $targets, null);
                        $totalIncentive = isset($sale->service_price_direct_incentive) ? $sale->service_price_direct_incentive :  $sale->direct_incentive;
                        $isActive = $targetService->employeeActive($sale, $month . '/' . $params['year']);
                        if ($isActive === true) {
                            if ($result['obj1'] === true) {
                                $totalIncentive = $valueIncentiveObj1;
                                if ($result['obj2'] === true) {
                                    $totalIncentive = $valueIncentiveObj2;
                                }
                            }
                        }

                        $detailedSale = $this->getDetailSale($sale);
                        $detailedSale['incentive'] = 0;
                        $detailedSale['tracking_id'] = $sale->tracking_id;
                        $totalDefSale += $sale->price * $sale->quantity;
                        $contSales += $sale->quantity;

                        // if ($sale->current_state == env('STATE_PAID')) {
                            $totalDefIncentive  += $totalIncentive * $sale->quantity;
                            $detailedSale['incentive'] = $totalIncentive * $sale->quantity;
                        //}
                        $detailSale[] = $detailedSale;
                    }
                }
            }
            $month++;
        }

        $success['total_incentives']  = $totalDefIncentive;
        $success['total_sales']       = $totalDefSale;
        $success['cont_sales']        = $contSales;
        $success['sales']             = $detailSale;

        return $success;
    }

    /**
     * Recoge informacion de un tracking en concreto
     */
    public function getTrackingInfo(Request $request) {
        try {
            $params = $request->all();
            $trackingSearch = Tracking::where('id', '=', $params['id'])->first();
            $collection = [];
            $collection['centre'] = Centre::getCentreByField($trackingSearch['centre_id']);
            $collection['centre_employee_id'] = Centre::getCentreByField($trackingSearch['centre_employee_id']);
            $collection['service'] = Service::GetService4Id($trackingSearch['service_id']);
    
            $collection['category'] = DB::table('service_categories')
                ->select('service_categories.name', 'service_categories.image_portrait')
                ->whereNull('service_categories.cancellation_date')
                ->where('service_categories.id', $collection['service'][0]['category_id'])
                ->get();
    
            $collection['patient_name'] = $trackingSearch['patient_name'];
            $collection['employee_id'] = $trackingSearch['employee_id'];
            $collection['quantity'] = $trackingSearch['quantity'];
            if ($trackingSearch['hc'] == null && $trackingSearch['dni'] == null) {
                $collection['idType'] = 'phone';
                $collection['patientId'] = $trackingSearch['phone'];
            } else if ($trackingSearch['phone'] == null && $trackingSearch['dni'] == null) {
                $collection['idType'] = 'hc';
                $collection['patientId'] = $trackingSearch['hc'];
            } else if ($trackingSearch['phone'] == null && $trackingSearch['hc'] == null) {
                $collection['idType'] = 'dni';
                $collection['patientId'] = $trackingSearch['dni'];
            }
            return $collection;
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Error de validación', 500);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Ha ocurrido un error al crear seguimiento, contacte con el administrador', 500);
        }
    }

    public function getDetailSale($sale) {
        $normalizeSale = [];
        switch ($sale->current_state) {
            case env('STATE_PENDING'):
                $normalizeSale['date'] = $sale->started_date;
                break;

            case env('STATE_APOINTMENT'):
                $normalizeSale['date'] = $sale->apointment_date;
                break;

            case env('STATE_SERVICE'):
                $normalizeSale['date'] = $sale->service_date;
                break;

            case env('STATE_INVOICED'):
                $normalizeSale['date'] = $sale->invoiced_date;
                break;

            case env('STATE_VALIDATE'):
                $normalizeSale['date'] = $sale->validation_date;
                break;

            case env('STATE_PAID'):
                $normalizeSale['date'] = $sale->validation_date;
                break;
        }
        $normalizeSale['started_date'] = date('d/m/Y', strtotime($sale->started_date));
        $normalizeSale['date'] = date('d/m/Y', strtotime($normalizeSale['date']));
        $normalizeSale['state']   = $sale->current_state;
        $normalizeSale['patient_name']   = $sale->patient_name;

        $normalizeSale['service'] = $sale->service;
        $normalizeSale['price'] = $sale->price;
        $normalizeSale['quantity'] = $sale->quantity;
        return $normalizeSale;
    }

    public function store(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'employee'              => 'required',
                'patient_name'          => 'required',
                'centre_id'             => 'required',
                'centre_employee_id'    => 'required',
                'service_id'            => 'required',
                'tracking_date'         => 'before:tomorrow',
                'patientId'             => 'required',
                'idType'                => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $params = [];
            $params = $request->all();

            $validate = Tracking::checkDate($params['tracking_date']);
            if ($validate === false) {
                return $this->sendError('Fecha fuera de periodo de corte, corrijalo por favor');
            }

            $employee = DB::table('employees')->where('username', $params['employee'])->first();
            if (empty($employee)) {
                return $this->sendError('Empleado no encontrado');
            }

            switch ($params['idType']) {
                case 'dni':
                    $params['dni'] = $params['patientId'];
                    break;
                case 'phone':
                    $params['phone'] = $params['patientId'];
                    break;
                case 'hc':
                    $params['hc'] = $params['patientId'];
                    break;
            }

            $params['started_user_id'] = $employee->id;
            $params['apointment_done'] = 0;
            $params['started_date']    = $params['tracking_date'];
            $params['employee_id']     = $employee->id;
            $params['state']           = env('STATE_PENDING');
            $params['state_date']      = $params['tracking_date'];
            $tracking_id = Tracking::create($params)->id;

            $success = ['tracking_id' => $tracking_id];
            return $this->sendResponse($success, 'Seguimiento creado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Error de validación', 500);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Ha ocurrido un error al crear seguimiento, contacte con el administrador', 500);
        }
    }
}
