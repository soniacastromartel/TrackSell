<?php


namespace App\Services;
use DB;
use App\Centre;
use App\Target;
use App\Employee;
use App\Exports\TargetsExport;
use Exception;

class TargetService
{

    public function getExportTarget($params, $forRanking = false)
    {
        $orderByField = 'service_date';
        $whereFields = 'true ';

        if (!isset($params['monthYear'])) {
            return [];
        }
        if (isset($params['centre']) && !empty($params['centre'])) {
            $idCentres = array_column($params['centre']->toArray(), 'id');
            $whereFields .= ' and centre_employee_id in (  ' . implode(",", $idCentres) . ')';
        }
        if (isset($params['employee']) && !empty($params['employee'])) {
            $whereFields .= ' and employee = \'' . $params['employee'] . '\'';

        }
        if (isset($params['monthYear']) && !empty($params['monthYear'])) {
            if (isset($params['acumulative']) && $params['acumulative']) {
                $year = substr($params['monthYear'], strpos($params['monthYear'], '/') + 1);
                $beginYear = intval($year) - 1;
                $whereLikeBegin = $beginYear . '-12-' . env('START_DAY_PERIOD');
                $whereLikeLast = $year . '-12-' . env('END_DAY_PERIOD');

            } else {
                $year = substr($params['monthYear'], strpos($params['monthYear'], '/') + 1);
                $currentMonth = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
                $previousMonth = $currentMonth - 1;
                $beginYear = $year;

                if ($currentMonth == "1") {
                    $previousMonth = "12";
                    $beginYear -= 1;
                }
                $whereLikeBegin = $beginYear . '-' . str_pad($previousMonth, 2, "0", STR_PAD_LEFT) . '-' . env('START_DAY_PERIOD');
                $whereLikeLast = $year . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');

            }

            $mActual = ltrim(date('m'), "0");
            $dActual = ltrim(date('d'), "0");
            $currentMonth = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
        }

        $exportData = DB::table('export_target')
            ->whereRaw($whereFields)
            ->orderBy('cod_business');

        $exportData = $exportData->where(function ($query) use ($whereLikeBegin, $whereLikeLast, $params) {
            if (isset($params['todos_estados']) && $params['todos_estados']) {
                $query->whereBetween('started_date', [$whereLikeBegin, $whereLikeLast])
                    ->orWhereBetween('apointment_date', [$whereLikeBegin, $whereLikeLast])
                    ->orWhereBetween('service_date', [$whereLikeBegin, $whereLikeLast])
                    ->orWhereBetween('invoiced_date', [$whereLikeBegin, $whereLikeLast])
                    ->orWhereBetween('validation_date', [$whereLikeBegin, $whereLikeLast]);
            } else {
                $query->whereBetween('validation_date', [$whereLikeBegin, $whereLikeLast]);
            }
        });

        $exportData = $exportData->get();
        $targetAux = [];
        // Exclusion de empleados del ranking
        if ($forRanking && count($exportData) > 1) {
            foreach ($exportData as $ed) {
                $employee = Employee::whereRaw("BINARY id = ?", [$ed->employee_id])->first();
                if (!$employee->excludeRanking) {
                    $targetAux[] = $ed;
                }
            }
        }
        if (!isset($params['centre'])) {
            $params['centre'] = null;
        }
        $colection = $forRanking ? $targetAux : $exportData;
        $exportData = $this->filterRequestChanges($colection, $params['centre']);
        return $exportData;
    }

    /**
     * Metodo que procesa las solicitudes de cambio de centro
     * que hay para datos validados
     * 
     * En caso que haya solicitudes en fecha para el empleado con seguimiento validado
     * cambiamos el centro por el destino de la solicitud
     * 
     */
    public function filterRequestChanges($exportData, $centres)
    {

        $idCentres = null;
        if (!empty($centres)) {
            $idCentres = array_column($centres->toArray(), 'id');
        }
        $resultData = collect([]);
        foreach ($exportData as $eData) {
            $copy = null;
            if (empty($eData->rc_centre_employee_id)) {
                $resultData->push($eData);
            }
            if (!empty($eData->rc_centre_employee_id) && in_array($eData->rc_centre_employee_id, $idCentres)) {
                $copy = $eData;
                $copy->centre_employee_id = $eData->rc_centre_employee_id;
                $copy->centre_employee = $eData->rc_centre_employee;
                $arrSupervisor = Employee::getSupervisorsActive($eData->rc_centre_employee_id)->toArray();
                $copy->supervisor = implode(',', array_column($arrSupervisor, 'id'));//FIXME.... error tipo
                $resultData->push($copy);
            }
        }
        return $resultData;
    }

    /**
     * Function que obtiene la venta cruzada
     */
    public function getVC($centres, $target)
    {
        $vcTotal = 0;

        $centres = isset($centres) ? $centres : Centre::getCentresActive();
        foreach ($centres as $centre) {
            if (isset($target[$centre->name])) {
                foreach ($target[$centre->name] as $i => $targetRow) {
                    $price = $targetRow->price;
                    if (!empty($targetRow->discount) && $targetRow->is_calculate === 1) {
                        $price = $targetRow->discount_price;
                    }
                    $vcTotal += $price * $targetRow->quantity;
                }
            }
        }

        return $vcTotal;
    }

    /**
     * Function que obtiene los target segun parametros pasados
     * 
     * @param $centre: @Illuminate\Support\Collection   nombre del centro / collection / null
     * @param $month: @string   Mes 
     * @param $year:  @string    Año 
     */
    public function getTarget($centres, $month, $year)
    {

        $whereFields = "";
        $month = ltrim($month, '0');
        if (!empty($centres)) {

            $idCentres = array_column($centres->toArray(), 'id');
            $whereFields .= " centre_id in (" . implode(",", $idCentres) . ")";

            if (empty($year)) {
                $year = date('Y');
            }
            //FIXME.... Sacar esta query fuera de llamada ( sólo hacerla una vez por centro )
            $whereFields .= " and ((month between 1 and 12)  and  year = " . $year . ')';

            $targetsDefined = DB::table('targets')
                ->whereRaw($whereFields)
                ->orderBy('month', 'asc')
                ->orderBy('year', 'asc')
                ->get();

            $targetByCentre = [];
            foreach ($targetsDefined as $target) {

                if (!isset($targetByCentre[$target->centre_id])) {
                    $targetByCentre[$target->centre_id] = [];

                }
                $targetByCentre[$target->centre_id][$target->month] = [];
                $targetByCentre[$target->centre_id][$target->month] = $target;
            }

            $targetsByCentre = [];
            foreach ($targetsDefined as $target) {

                $mDefined = $target->month;
                /**
                 * Get target - segun mes/año solicitado
                 * 
                 * calc_month - te dice target
                 */
                if ($month . '/' . $year == $target->month . '/' . $target->year) {

                    if (!isset($targetsByCentre[$target->centre_id])) {
                        $targetsByCentre[$target->centre_id] = [];
                    }
                    $monthBefore = $mDefined - 1;
                    $calcMonth = $targetByCentre[$target->centre_id][$mDefined]->calc_month;
                    $nextMonthYear = 1;
                    if (!empty($targetByCentre[$target->centre_id][$monthBefore]->calc_month)) {
                        $monthYearBef = explode("/", $targetByCentre[$target->centre_id][$monthBefore]->calc_month);
                        $nextMonthYear = intval($monthYearBef[0]);
                        if ($targetByCentre[$target->centre_id][$monthBefore]->obj1_done) {
                            if ($monthYearBef[0] < 12) {
                                $nextMonthYear += 1;
                                $calcMonth = $nextMonthYear . '/' . $monthYearBef[1];
                            } else {
                                $nextMonthYear = 1;
                                $nextYear = intval($monthYearBef[1]) + 1;
                                $calcMonth = $nextMonthYear . '/' . $nextYear;
                            }
                        } else {
                            $calcMonth = $monthYearBef[0] . '/' . $monthYearBef[1];
                        }

                    }
                    $targetData = Target::find($target->id);
                    if (empty($target->calc_month)) {
                        $targetData->update(['calc_month' => $calcMonth]);
                    }
                    $targetsByCentre[$target->centre_id] = $targetByCentre[$target->centre_id][$nextMonthYear];

                    $vd = 0;
                    if (isset($targetByCentre[$target->centre_id][$month])) {
                        $vd = $targetByCentre[$target->centre_id][$month]->vd;
                    }
                    $targetsByCentre[$target->centre_id]->vd = $vd;

                }

            }
        }

        return $targetsByCentre;
    }

    /**
     * Function que revisa si se cumples objetivos de VC y VP (VC + VD -- definida por targetsDefined)
     */
    public function stateTarget($targetDef, $monthYear, $tracking, $centres)
    {
        $done = 1;
        $notdone = 0; //Pendiente obj1 

        $stateVc = $notdone;
        $stateVp = $notdone;

        //Loop centres (specific)
        foreach ($centres as $centre) {
            if (!isset($targetDef[$centre->id])) {
                continue;
            }
            $targetDefined = $targetDef[$centre->id];
            $params['monthYear'] = $monthYear;
            $params['centre'] = $centres;
            $vcTotal = $this->getVC($params['centre'], $tracking);
            $vpTotal = isset($targetDefined->vd) ? $targetDefined->vd : 0;
            $year = substr($monthYear, strpos($monthYear, '/') + 1);
            $target = Target::where(['centre_id' => $targetDefined->centre_id, 'year' => $year])->get();

            $monthCalc = null;
            $i = 0;
            foreach ($target as $t) {
                if (!empty($t->calc_month)) {
                    // $lastCalcMonth = $t->calc_month;
                    if ($t->obj1_done == 1) {
                        $arrMonthYear = explode("/", $t->calc_month);
                        $calcMonth = "";
                        if ($arrMonthYear[0] < 12) {
                            $nextMonth = $arrMonthYear[0] + 1;
                            $lastCalcMonth = $nextMonth . "/" . $arrMonthYear[1];
                        } else {
                            $nextYear = $arrMonthYear[1] + 1;
                            $lastCalcMonth = "1/" . $nextYear;
                        }
                    }
                }

                if ($t->month . "/" . $t->year == ltrim($monthYear, '0')) {
                    $targetData = Target::find($t->id);
                }
                $arrMonthCalc = explode("/", $monthYear);
                $i++;
            }

            foreach ($target as $t) {
                $targetToUpdate = $targetData;
                if ($t->month . "/" . $t->year == $targetData->calc_month) {
                    $targetData = Target::find($t->id);
                    break;
                }
            }

            if ($vcTotal >= $targetData->obj1) {
                $stateVc = $done;
                $fields['obj1_done'] = true;
            } else {
                $fields['obj1_done'] = false;
            }

            if ($vpTotal > $targetData->obj2) {
                $stateVp = $done;
                $fields['obj2_done'] = true;
            } else {
                $fields['obj2_done'] = false;
            }
            $targetToUpdate->update($fields);

            if ($vcTotal >= $targetData->obj1) {
                $stateVc = $done;
            }

            if ($vpTotal > $targetData->obj2) {
                $stateVp = $done;
            }
        }
        return ['vc' => $stateVc, 'vp' => $stateVp];
    }

    /** 
     * Funcion que nos comprueba excepciones
     * 
     * 1.- Si supervisor tiene ventas, no incentivar las mismas (control de varios centros)
     * 2.- Control de obj1 cumplido
     * 
     * */
    public function rules($targetRow, $targets, $supervisors)
    {
        $obj1 = true;
        $obj2 = true;

        if ((!empty($targets) && $targets['vc'] == 0) || (empty($targets))) {
            $obj1 = false;
        }

        if ((!empty($targets) && $targets['vp'] == 0) || (empty($targets))) {
            $obj2 = false;
        }

        if (!empty($supervisors) && in_array($targetRow->employee_id, $supervisors)) {
            $obj1 = false;
            $obj2 = false;
        }

        return ['obj1' => $obj1, 'obj2' => $obj2];
    }


    public function filterTarget($target, $month, $year)
    {
        $targetResult = [];

        $prevMonth = $month - 1;
        $prevYear = $year;
        $lastYear = $year;
        if ($month == 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        }

        $minDate = $prevYear . '-' . str_pad($prevMonth, 2, "0", STR_PAD_LEFT) . '-20';
        $maxDate = $lastYear . '-' . str_pad($month, 2, "0", STR_PAD_LEFT) . '-20';
        foreach ($target as $targetRow) {
            if ($targetRow->validation_date > $minDate && $targetRow->validation_date <= $maxDate) {
                $targetResult[] = $targetRow;
            }
        }
        return $targetResult;

    }
    /**
     * Function obtener ranking segun parametros
     * 
     * @param $centre nombre de centro / null para obtener todos los centros
     * @param $month Mes
     * @param $year  Year
     * 
     */
    public function getRanking($target, $centres = null, $month, $year, $acumulative = false)
    {
        // Get target : obtener objetivo según  
        //1.- si es conseguido en mes de filtro, igual al objetivo de ese mes
        //2.- si no es conseguido en mes de filtro  de mes anterior que no tenga objetivo conseguido
        //$centre = (isset($centre)) ? $centre: Centre::getCentresActive();
        $target = $this->normalizeData($target);
        $month = $acumulative ? 12 : $month;
        $ranking = [];
        $beginMonth = $acumulative ? 1 : $month;
        $targetDefined = $this->getTarget($centres, $beginMonth, $year);
        foreach ($centres as $centre) {
            $centre = isset($centre->name) ? $centre->name : $centre;
            if (isset($target[$centre])) {
                $beginMonth = $acumulative ? 1 : $month;
                while ($beginMonth <= $month) {
                    $targetDefined = $this->getTarget($centres, $beginMonth, $year);
                    if (empty($targetDefined) && !empty($centre)) {
                        throw new \Exception("Error no se ha definido objetivo para el centro: " . $centre);
                    }
                    foreach ($target[$centre] as $i => $targetRow) {

                        if ($i == 0) {
                            $targets = [];
                            if (!empty($targetDefined)) {
                                $centreData = $centres->filter(function ($centreFind) use ($centre) {
                                    if ($centre == $centreFind->name) {
                                        return $centreFind;
                                    }
                                });
                                //$target[$centre] = $this->filterTarget($target[$centre], $beginMonth , $year); 
                                $targetCentre = $this->filterTarget($target[$centre], $beginMonth, $year);

                                $targets = $this->stateTarget($targetDefined, $beginMonth . '/' . $year, [$centre => $targetCentre], $centreData);
                            }
                        }
                        if (!isset($ranking[$targetRow->employee_id])) {
                            $ranking[$targetRow->employee_id] = $targetRow;
                            $ranking[$targetRow->employee_id]->total_incentive = 0;
                            $ranking[$targetRow->employee_id]->total_price = 0;
                        }

                        $prevMonth = $beginMonth - 1;
                        $prevYear = $year;
                        $lastYear = $year;
                        if ($beginMonth == 1) {
                            $prevMonth = 12;
                            $prevYear = $year - 1;
                        }

                        $minDate = $prevYear . '-' . str_pad($prevMonth, 2, "0", STR_PAD_LEFT) . '-20';
                        $maxDate = $lastYear . '-' . str_pad($beginMonth, 2, "0", STR_PAD_LEFT) . '-20';
                        if ($targetRow->validation_date > $minDate && $targetRow->validation_date <= $maxDate) {
                            $result = $this->rules($targetRow, $targets, null);

                            $valueIncentiveObj1 = isset($targetRow->service_price_incentive1) ? $targetRow->service_price_incentive1 : $targetRow->obj1_incentive;
                            $valueIncentiveObj2 = isset($targetRow->service_price_incentive2) ? $targetRow->service_price_incentive2 : $targetRow->obj2_incentive;
                            $totalIncentive = 0;
                            $totalIncentive = isset($targetRow->service_price_direct_incentive) ? $targetRow->service_price_direct_incentive : $targetRow->direct_incentive;

                            $isActive = $this->employeeActive($targetRow, $month . '/' . $year);
                            if ($isActive === true) {
                                if ($result['obj1'] === true) {
                                    $totalIncentive = $valueIncentiveObj1;
                                    if ($result['obj2'] === true) {
                                        $totalIncentive = $valueIncentiveObj2;
                                    }
                                }
                            }
                            $ranking[$targetRow->employee_id]->total_incentive += $totalIncentive * $targetRow->quantity;
                            $ranking[$targetRow->employee_id]->total_price += $targetRow->price * $targetRow->quantity;
                        }
                    }
                    $beginMonth++;
                }
            }
        }
        $rankingFinal = [];

        $employeeInfo = Employee::select('employees.id as employee_id', 'centres.name as centre')
            ->whereIn('employees.id', array_keys($ranking))
            ->join('centres', 'centres.id', '=', 'centre_id')
            ->get();

        foreach ($employeeInfo as $ek => $emp) {
            foreach ($ranking as $employeeId => $rankingData) {
                if ($employeeId == $emp->employee_id) {
                    $rankingFinal[$employeeId] = $rankingData;
                    $rankingFinal[$employeeId]->centre_employee = $emp->centre;
                    continue;
                }
            }
        }

        $keys = array_column($rankingFinal, 'total_price');
        array_multisort($keys, SORT_DESC, $rankingFinal);

        foreach ($rankingFinal as $index => &$rankData) {
            $rankAux = [
                'position' => $index + 1
                ,
                'employee' => $rankData->employee
                ,
                'centre' => $rankData->centre_employee
                ,
                'total_price' => $rankData->total_price
                ,
                'total_incentive' => $rankData->total_incentive
            ];
            $rankData = $rankAux;
        }

        return $rankingFinal;
    }


    public function normalizeData($target)
    {
        $tracking = [];
        //1.-  Agrupar por centro prescriptor
        foreach ($target as $targetRow) {
            if (!isset($tracking[$targetRow->centre_employee])) {
                $tracking[$targetRow->centre_employee] = [];
            }
        }
        foreach ($target as $targetRow) {
            $tracking[$targetRow->centre_employee][] = $targetRow;
        }
        return $tracking;
    }

    /* 
     */
    private function getDiscount($targetRow, $field)
    {
        $result = $targetRow->$field;
        if (!empty($targetRow->discount) && $targetRow->is_calculate === 1) {

            if (strpos($field, 'service_price_') !== false) {
                $field = substr($field, strlen('service_price_'));
            }

            $fieldDiscount = 'discount_' . $field;
            $result = $targetRow->$fieldDiscount;
        }
        return $result;
    }

    /**
     * Función que devuelve un array para el centro seleccionado con:
     * total_incentive: suma incentivos empleados
     * total_super_incentive: suma incentivos supervisor
     * total_income:suma ingreso total
     * details []: array de objetos Employee con sus datos, más incentivos por empleado y si es supervisor o no.
     * 
     * @param $tracking consulta a la vista 'export-target'
     * @param $targetDefined registro(s) de la tabla 'targets'
     * @param $monthYear mes seleccionado
     * @param $centres
     * @param $targetCentre registros para obtener VCTotal
     */


    public function getSummarySales($tracking, $targetDefined, $monthYear, $centres, $targetCentre)
    {
        try {
            $total = [];
            $totalCentre = [];
            $centre = array_values($centres->toArray())[0];
            if (is_object($centre)) {
                $centre = (array) $centre;
            }
            $targets = [];
            $totalSupervisors = [];
            foreach ($tracking[$centre['name']] as $i => $trackingRow) {
                // Cogemos el grupo de supervisores por centro
                if ($i == 0) {
                    if (!empty($targetDefined)) {
                        $targets = $this->stateTarget($targetDefined, $monthYear, $targetCentre, $centres);
                    }
                    $totalCentre[$centre['name']]['total_incentive'] = 0;
                    $totalCentre[$centre['name']]['total_super_incentive'] = 0;
                    $totalCentre[$centre['name']]['total_income'] = 0;
                }
                if (!isset($total[$trackingRow->employee_id])) {
                    $total[$trackingRow->employee_id]['name'] = $trackingRow->employee;
                    $total[$trackingRow->employee_id]['nombre_a3'] = $trackingRow->nombreA3;
                    $total[$trackingRow->employee_id]['dni'] = $trackingRow->dni;
                    $total[$trackingRow->employee_id]['cod_business'] = $trackingRow->cod_business;
                    $total[$trackingRow->employee_id]['cod_employee'] = $trackingRow->cod_employee;
                    $total[$trackingRow->employee_id]['total_incentive'] = 0;
                    $total[$trackingRow->employee_id]['total_super_incentive'] = 0;
                    $total[$trackingRow->employee_id]['total_income'] = 0;
                    $total[$trackingRow->employee_id]['is_supervisor'] = 0;
                    $total[$trackingRow->employee_id]['tracking_ids'] = [];
                }

                $valueIncentiveObj1 = $this->getDiscount($trackingRow, 'service_price_incentive1');
                $valueIncentiveObj2 = $this->getDiscount($trackingRow, 'service_price_incentive2');
                $totalIncentive = 0;

                $result = $this->rules($trackingRow, $targets, null);
                $totalIncentive = isset($trackingRow->service_price_direct_incentive) ? $trackingRow->service_price_direct_incentive : $trackingRow->direct_incentive;

                $isActive = $this->employeeActive($trackingRow, $monthYear);
                if ($isActive === true) {
                    if ($result['obj1'] === true) {
                        $totalIncentive = $valueIncentiveObj1;
                        if ($result['obj2'] === true) {
                            $totalIncentive = $valueIncentiveObj2;
                        }
                    }
                }
                // Si la recomendacion lleva descuento y es el familiar, no aplica incentivo
                if ($trackingRow->discount !== null && $trackingRow->discount === 'DESCUENTO1') {
                    $total[$trackingRow->employee_id]['total_incentive'] += 0;
                    $total[$trackingRow->employee_id]['total_income'] += 0;
                    //$total[$trackingRow->employee_id]['total_income']     = 0;
                    $total[$trackingRow->employee_id]['tracking_ids'][] = 0;
                    $totalCentre[$centre['name']]['total_incentive'] += 0;
                } else {
                    $total[$trackingRow->employee_id]['total_incentive'] += $totalIncentive * $trackingRow->quantity;
                    $total[$trackingRow->employee_id]['total_income'] += $totalIncentive * $trackingRow->quantity;
                    $total[$trackingRow->employee_id]['total_income'] = round($total[$trackingRow->employee_id]['total_income'], 2);
                    $total[$trackingRow->employee_id]['tracking_ids'][] = $trackingRow->tracking_id;
                    $totalCentre[$centre['name']]['total_incentive'] += $totalIncentive * $trackingRow->quantity;
                }

                $supervisors = explode(",", $trackingRow->supervisor);
                foreach ($supervisors as $supervisorId) {
                    $supervisorId = trim($supervisorId);
                    if (!in_array($supervisorId, $totalSupervisors)) {
                        $totalSupervisors = array_merge($totalSupervisors, [$supervisorId]);
                    }

                    if (!empty($supervisorId)) {
                        $supervisor = Employee::find($supervisorId);
                        $isSupervisorActive = $this->employeeActive($supervisor, $monthYear);
                        if (!isset($total[$supervisorId])) {
                            $total[$supervisorId]['name'] = $supervisor->name;
                            $total[$supervisorId]['nombre_a3'] = $supervisor->nombre_a3;
                            $total[$supervisorId]['dni'] = $supervisor->dni;
                            $total[$supervisorId]['cod_business'] = $supervisor->cod_business;
                            $total[$supervisorId]['cod_employee'] = $supervisor->cod_employee;
                            $total[$supervisorId]['total_incentive'] = 0;
                            $total[$supervisorId]['total_super_incentive'] = 0;
                            $total[$supervisorId]['total_income'] = 0;
                        }

                        $valueSuperIncentive1 = $this->getDiscount($trackingRow, 'service_price_super_incentive1');
                        $valueSuperIncentive2 = $this->getDiscount($trackingRow, 'service_price_super_incentive2');

                        $result = $this->rules($trackingRow, $targets, array_values($supervisors));
                        $auxIncentive = 0;
                        //Solo aplica bonus de venta, para empleados activos en fecha fin de corte
                        if ($isActive === true) {
                            if ($result['obj1'] === true) {
                                $auxIncentive = $valueSuperIncentive1;
                                if ($result['obj2'] === true) {
                                    $auxIncentive = $valueSuperIncentive2;
                                }
                            }
                            if ($auxIncentive == 0) {
                                $auxIncentive = 0;
                            }
                        }
                        //SUPERINCENTIVE SUPERV
                        if (!in_array($trackingRow->employee_id, $supervisors)) {
                            // Si la recomendacion lleva descuento y es el familiar, no aplica incentivo
                            if ($trackingRow->discount !== null && $trackingRow->discount === 'DESCUENTO1') {
                                $total[$trackingRow->employee_id]['total_super_incentive'] = 0;
                            } else {
                                $total[$trackingRow->employee_id]['total_super_incentive'] += $auxIncentive * $trackingRow->quantity;
                            }
                        }
                        //Solo aplica bonus de venta, para supervisores activos en fecha fin de corte
                        if ($isSupervisorActive == false) {
                            $total[$supervisorId]['total_super_incentive'] = 0;
                        }

                        // Segunda comprobacion antes de asignacion por si no procede aplicar incentivo
                        if ($trackingRow->discount === null || $trackingRow->discount !== 'DESCUENTO1') {
                            $total[$supervisorId]['total_super_incentive'] += $auxIncentive * $trackingRow->quantity;
                            $total[$supervisorId]['total_super_incentive'] = round($total[$supervisorId]['total_super_incentive'], 2);

                            $total[$supervisorId]['total_income'] = $total[$supervisorId]['total_incentive'];
                            if ($centre['id'] != env('ID_CENTRE_HCT')) {
                                $total[$supervisorId]['total_income'] += $total[$supervisorId]['total_super_incentive'];
                            }
                            $total[$supervisorId]['total_income'] = round($total[$supervisorId]['total_income'], 2);
                        }

                        $total[$supervisorId]['is_supervisor'] = 1;
                    }
                }
                if (!isset($auxIncentive)) {
                    $auxIncentive = 0;
                }

                if ($trackingRow->discount === null || $trackingRow->discount !== 'DESCUENTO1') {
                    // $auxIncentive == null ?  $auxIncentive = 0: $auxIncentive;
                    $totalCentre[$centre['name']]['total_super_incentive'] += $auxIncentive * $trackingRow->quantity;
                    $totalCentre[$centre['name']]['total_super_incentive'] = round($totalCentre[$centre['name']]['total_super_incentive'], 2);
                }

                $eIds = array_keys($total);
                if ($i == count($tracking[$centre['name']]) - 1) {
                    $employees = Employee::select('employees.id as employee_id', 'centres.id as centre_id', 'centres.name as centre_name', 'employees.cancellation_date')
                        ->leftJoin('centres', 'centres.id', '=', 'employees.centre_id')
                        ->whereIn('employees.id', $eIds)
                        ->get();
                    foreach ($employees as $employee) {
                        $total[$employee->employee_id]['centre'] = empty($employee->centre_name) ? '' : $employee->centre_name;
                        $total[$employee->employee_id]['cancellation_date'] = $employee->cancellation_date;
                    }
                    if ($centre['id'] == env('ID_CENTRE_HCT')) {
                        foreach ($supervisors as $supervisorId) {
                            $supervisorId = trim($supervisorId);
                            $total[$supervisorId]['total_super_incentive'] /= count($supervisors);
                            $total[$supervisorId]['total_super_incentive'] = round($total[$supervisorId]['total_super_incentive'], 2);
                            $total[$supervisorId]['total_income'] += $total[$supervisorId]['total_super_incentive'];
                        }
                    }
                    $totalCentre[$centre['name']]['details'] = $total;
                    $totalCentre[$centre['name']]['total_income'] += $totalCentre[$centre['name']]['total_incentive'] + $totalCentre[$centre['name']]['total_super_incentive'];
                }
            }
            return $totalCentre;
        } catch (Exception $e) {
            \Log::debug($e);
        }
    }

    //Tener en cuenta usuarios dados de baja antes de fecha de corte
    /** 
     * Regla: solo aplicar incentivo directo
     * Regla: supervisor no bonifica esta venta
     * Regla: si es supervisor, no hay bonus de venta
     * 
     */
    function employeeActive($employee, $monthYear)
    {
        $month = substr($monthYear, 0, strpos($monthYear, '/'));
        $year = substr($monthYear, strpos($monthYear, '/') + 1);
        $dateEndIncentive = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');
        $isActive = false;
        if (empty($employee->cancellation_date) || $employee->cancellation_date >= $dateEndIncentive) {
            $isActive = true;
        }
        return $isActive;
    }

    public function updatePrivateSales($amount, $centre, $date)
    {
        try {
            $dateObject = new \DateTime($date);
            $year = $dateObject->format('Y');
            $month = $dateObject->format('m');
            $updateData = [
                'vd' => floatval($amount),
            ];

            $updatedTarget = Target::updateTarget($year, $month, $centre, $updateData);
            return response()->json([
                'success' => true,
                'message' => 'Objetivo actualizado correctamente.',
                'data' => $updatedTarget
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

}