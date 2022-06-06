<?php

namespace App\Exports;

// use App\Target;
use App\Employee;
use App\Centre;
//use DB; 
use App\Services\TargetService; 

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
// use Exception; 

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TargetsExport implements FromCollection, WithStyles, WithEvents
{
    use Exportable; 

    private $targetDefined; 

    public function __construct($target = null, $filters=[])
    {
        $this->target = $target;
        $this->filters = $filters; 
        $this->spreadSheet = null; 
        $this->rows = 0;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        return $this->target;
    }

    public function styles(Worksheet $sheet)
    {
        
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function(BeforeExport $event){
                
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path('templates/export_target.xls')),Excel::XLS);
                $this->spreadSheet = $event->writer->getDelegate();
                
                $this->generateContentFirstSheet($this->target,$event); 

                $this->rows = 9;
                
                $this->generateContentSecondSheet($this->target,$event); 

                return $event->getWriter()->getSheetByIndex(0);
            },
            AfterSheet::class => function(AfterSheet $event){
                $sheets = $this->spreadSheet->getSheetCount(); 
                $this->spreadSheet->removeSheetByIndex($sheets-1);
            }
        ];
    }

    private  function normalizeData($target){
        $tracking = []; 
        //1.-  Agrupar por centro prescriptor
        foreach ($target as $targetRow) {
            if (!isset($tracking[$targetRow->centre_employee])) {
                $tracking[$targetRow->centre_employee] = []; 
            }
            $tracking[$targetRow->centre_employee] = []; 
        }
        foreach ($target as $targetRow) {
            $tracking[$targetRow->centre_employee][] = $targetRow; 
        }
        return $tracking; 
    }

    private function generateContentFirstSheet($target,$event){
        $tracking = $this->normalizeData($target); 
        
        $this->printHeader(array_keys($tracking), $event->writer->getSheetByIndex(0)); 

        $this->rows = 9; 
        $centre = $this->filters['centre'] == 'TODOS' ? Centre::getCentresActive() : Centre::where('name',$this->filters['centre'])->get();

        $this->printContentFirstSheet($tracking,$centre, $event->writer->getSheetByIndex(0));
    }

    private function printHeader($centre, $sheet){
        
        $sheet->setCellValue('C3',$this->filters['employee']);
        $sheet->setCellValue('C4',$this->filters['centre']);
        $sheet->setCellValue('C5','TODOS');
        $sheet->setCellValue('C6','TODOS');
        $sheet->setCellValue('H3',$this->filters['date_from']);
        $sheet->setCellValue('H4',$this->filters['date_to']);
    }

    private function getDiscount ($targetRow, $field) {
        $result = $targetRow->$field; 
        if (!empty($targetRow->discount) && $targetRow->is_calculate === 1) {

            if (strpos($field, 'service_price_') !== false) {
                $field = substr($field, strlen('service_price_')); 
            }

            $fieldDiscount = 'discount_'.$field;
            $result = $targetRow->$fieldDiscount; 
        }
        return $result; 
    }
    /**
     * Function que pone datos calculados para filtros aplicados ( mes/año, centro, empleado )
     * 
     * params: 
        * $target : list of targets in centre
        * $centre : centre name
        * $sheet  : xls sheet template to put data
     * 
     */
    private function printContentFirstSheet($target,$centres, $sheet){

        $aligmentLeft = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
        
        for($col = 'A'; $col !== 'J'; $col++) {
            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
        }

        // Get target : obtener objetivo según  
        //1.- si es conseguido en mes de filtro, igual al objetivo de ese mes
        //2.- si no es conseguido en mes de filtro  de mes anterior que no tenga objetivo conseguido
        $targetService = new TargetService();
        $this->targetDefined = $targetService->getTarget($centres, $this->filters['month'], $this->filters['year']); 
        
        if (empty($this->targetDefined)) {
            $cNames = [];
            foreach($centres->toArray() as $centre) {
                $cNames[] = is_array($centre) ? $centre['name']: $centre->name;
            } 
            $centresName = implode(',', $cNames); 
            throw new \Exception("Error no se ha definido objetivo para el centro: ".$centresName);
        }
        
        foreach ($centres as $centre) {
            if (isset($target[$centre->name])) {
                $totalSupervisors = []; 
                $totalIncomeSuperv = 0; 
                foreach ($target[$centre->name] as $i =>$targetRow) {
                    $sheet->getRowDimension($this->rows)->setRowHeight(15);
                    $sheet->getStyle('A'.$this->rows.':J'.$this->rows)->getAlignment()->setHorizontal($aligmentLeft);
        
                    $serviceDate    = date('d/m/Y',strtotime($targetRow->service_date));
                    $apointmentDate = date('d/m/Y',strtotime($targetRow->apointment_date));
                    $validationDate = date('d/m/Y',strtotime($targetRow->validation_date));
        
                    $sheet->setCellValue('A'.$this->rows,$targetRow->centre_employee);
                    $sheet->setCellValue('B'.$this->rows,$targetRow->patient_name);
                    $sheet->setCellValue('C'.$this->rows,$targetRow->hc);
                    $sheet->setCellValue('D'.$this->rows,$targetRow->service);
                    $sheet->setCellValue('E'.$this->rows,$targetRow->quantity);
                    $sheet->setCellValue('F'.$this->rows,$targetRow->employee);
                    $sheet->setCellValue('G'.$this->rows,$apointmentDate);
                    $sheet->setCellValue('H'.$this->rows,$serviceDate);
                    $sheet->setCellValue('I'.$this->rows,$validationDate);
                    $sheet->setCellValue('J'.$this->rows,$targetRow->discount_name);
                    $sheet->setCellValue('K'.$this->rows,$this->getDiscount ($targetRow, 'price'));

                    $valueIncentiveObj1 = $this->getDiscount ($targetRow, 'service_price_incentive1');
                    $valueIncentiveObj2 = $this->getDiscount ($targetRow, 'service_price_incentive2');

                    $totalIncentive = 0; 
                    if ($i == 0) {
                        if (!empty($this->targetDefined)) {
                            
                            $centreData = $centres->filter(function ($centreFind) use ($centre) {
                                if ($centre->name == $centreFind->name) {
                                    return $centreFind; 
                                }
                            });
                            $params = $this->filters; 
                            unset($params['employee']); 
                            $params['monthYear'] = $params['month'] . '/' . $params['year']; 
                            $params['centre']    = $centreData; 
                            $salesCentres =  $targetService->getExportTarget($params);
                            $targetCentre = $targetService->normalizeData($salesCentres); 

                            $targets = []; 
                            $targets = $targetService->stateTarget($this->targetDefined, $this->filters['month'].'/'.$this->filters['year'],$targetCentre,$centreData);
                        }
                    }
                    
                    $result = $targetService->rules($targetRow, $targets,null);
        
                    $totalIncentive = $this->getDiscount ($targetRow, 'service_price_direct_incentive');

                    $isActive = $targetService->employeeActive($targetRow,$params['month'] . '/' . $params['year']); 
                    if ($isActive === true) {
                        if ($result['obj1'] === true){
                            $totalIncentive = $valueIncentiveObj1;
                            if ($result['obj2'] === true){
                                $totalIncentive = $valueIncentiveObj2;
                            }
                        }
                    }
                    $sheet->setCellValue('L'.$this->rows,$totalIncentive * $targetRow->quantity);
                    $sheet->setCellValue('O'.$this->rows,$targetRow->observations);
                    
                    // Cogemos el grupo de supervisores por centro
                    if ($i == 0) {
                        $totalSuperIncentive  = [];
                    }
                    //REPARTO BONUS HCT - Agrupamos todos los supervisores que haya - Multisupervisor
                    $supervisors = explode(", ", trim($targetRow->supervisor));
                    foreach ($supervisors as $superv) { 
                        if (!in_array($superv, $totalSupervisors )){
                            $totalSupervisors = array_merge($totalSupervisors, [$superv]);
                        }
                    }

                    $totalBonus = 0; 
                    //REPARTO BONUS HCT
                    foreach ($supervisors as $supervisorId ) {
                        $supervisorId = trim($supervisorId);
                        $supervisor = Employee::find($supervisorId);
                        $isSupervisorActive = $targetService->employeeActive($supervisor, $params['month'] . '/' . $params['year']);
                        if (!isset($totalSuperIncentive[$supervisorId])) {
                            $totalSuperIncentive[$supervisorId] = 0; 
                            $totalIncome[$supervisorId] = 0; 
                            $totalColIncentive[$supervisorId] = 0; 
                        }
                        
                        $valueSuperIncentive1 = $this->getDiscount ($targetRow, 'service_price_super_incentive1');
                        $valueSuperIncentive2 = $this->getDiscount ($targetRow, 'service_price_super_incentive2');
                        $result = $targetService->rules($targetRow, $targets,array_values($supervisors));
                        $auxIncentive = 0;
                        //Solo aplica bonus de venta, para empleados activos en fecha fin de corte 
                        if ($isActive === true) {
                            if ($result['obj1'] === true){
                                $auxIncentive  = $valueSuperIncentive1;
                                if ($result['obj2'] === true){
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
                            if ($centre->id == env('ID_CENTRE_HCT') && $totalBonus == 0 
                                || $centre->id != env('ID_CENTRE_HCT')
                            )  {
                                if ($supervisorId  == $targetRow->employee_id) {
                                    $totalIncomeSuperv += $totalIncentive * $targetRow->quantity;
                                }
                                $totalBonus += $auxIncentive * $targetRow->quantity;
                            }
                        }

                        
        
                        $sheet->setCellValue('N'.$this->rows,$totalIncentive * $targetRow->quantity);
                        if ($auxIncentive == 0 && $supervisorId  == $targetRow->employee_id){
                            $totalIncome[$supervisorId] +=  $totalIncentive * $targetRow->quantity;
                        }
                        $totalColIncentive[$supervisorId] += $totalIncentive * $targetRow->quantity; 
                    }
                    /** SUMAMOS BONUS DE VENTA DE TODOS LOS SUPERVISORES DE LA FECHA DEL SEGUIMIENTO */               
                    $sheet->setCellValue('M'.$this->rows,$totalBonus);
                    $this->rows++;    
                    /** FILA BONUS SUPERVISOR */
                    //REPARTO BONUS HCT
                    if ($i == count($target[$centre->name]) -1) {
                        
                        foreach ($totalSupervisors as $supervisorId ) {
                            if (!empty($supervisorId)){
                                $supervisorId = trim($supervisorId);
                                $style =  [
                                    'fill' => [
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_MEDIUMGRAY
                                    ],
                                    'font' => [
                                        
                                        'bold' => true
                                    ]
                                ];
                                $sheet->getStyle('A'.$this->rows.':M'.$this->rows)->applyFromArray(
                                    $style
                                );
                                $supervisor = Employee::find($supervisorId); 
                                $sheet->getRowDimension($this->rows)->setRowHeight(15);
                                $sheet->setCellValue('A'.$this->rows,$targetRow->centre_employee);
                                $sheet->setCellValue('D'.$this->rows,'Bonus responsable de venta');
                                $sheet->setCellValue('F'.$this->rows,$supervisor->name);
                                $sheet->setCellValue('I'.$this->rows, env('END_DAY_PERIOD').'/'.str_pad($this->filters['month'], 2,"0", STR_PAD_LEFT) .'/'.$this->filters['year']);
                                
                                $totalSuperInc    = $totalSuperIncentive[$supervisorId]; 
                                $totalSuperIncome = $totalIncome[$supervisorId];
                                if ($centre->id == env('ID_CENTRE_HCT')) {
                                    $totalSuperInc /=  count($supervisors);
                                }
                                $totalSuperIncome += $totalSuperInc; 
                                $sheet->setCellValue('M'.$this->rows,$totalSuperInc);
                                $sheet->setCellValue('N'.$this->rows,$totalSuperIncome);
                                $this->rows++;
                            }
                            
                        }
                    } 
                }
            } else {
                if (!empty($this->targetDefined)) {
                
                    $centreData = $centres->filter(function ($centreFind) use ($centre) {
                        if ($centre->name == $centreFind->name) {
                            return $centreFind; 
                        }
                    });
                    $params = $this->filters; 
                    unset($params['employee']); 
                    $params['monthYear'] = $params['month'] . '/' . $params['year']; 
                    $params['centre']    = $centreData; 
                    $salesCentres =  $targetService->getExportTarget($params);
                    $targetCentre = $targetService->normalizeData($salesCentres); 
    
                    $targets = []; 
                    $targets = $targetService->stateTarget($this->targetDefined, $this->filters['month'].'/'.$this->filters['year'],$targetCentre,$centreData);
                }
            }
        }
    } 
    
    // Fill second page objetivos/seguimiento
    private function generateContentSecondSheet($target,$event){

        $tracking = $this->normalizeData($target); 
        
        $this->printHeader(array_keys($tracking), $event->writer->getSheetByIndex(1)); 

        $centres = Centre::whereIn('name',array_keys($tracking))->get(); 

        if (!empty($centres->toArray())) {
            $this->printContentSecondSheet($tracking,$centres, $event->writer->getSheetByIndex(1));
        }
    }

    private function printContentSecondSheet($target,$centres, $sheet){

        // Get target : obtener objetivo según  
        //1.- si es conseguido en mes de filtro, igual al objetivo de ese mes
        //2.- si no es conseguido en mes de filtro  de mes anterior que no tenga objetivo conseguido
        $targetService = new TargetService();
        $total = [];  
        $totalCentre = []; 

        foreach ($centres as $centre) {
            $centresData = Centre::where('name', $centre->name)->get(); 
            $vcTotal =  $targetService->getVC($centresData, $target); 
            $totalCentre = $targetService->getSummarySales([$centre->name => $target[$centre->name]], $this->targetDefined, $this->filters['month'] .'/'.$this->filters['year'], $centresData, $vcTotal); 
        
            foreach ($totalCentre as $tc => $totalDetail){
                $sheet->setCellValue('A'.$this->rows, $centre->name);
                $sheet->setCellValue('B'.$this->rows, $totalDetail['total_incentive']);
                $sheet->setCellValue('C'.$this->rows, $totalDetail['total_super_incentive']);
                $sheet->setCellValue('D'.$this->rows, $totalDetail['total_income']);
                $sheet->getStyle('A'.$this->rows.':D'.$this->rows)->getFont()->setBold(true);
                $this->rows++;
                foreach($totalDetail['details'] as $total){
                    if ($total['is_supervisor'] == 1) {
                        $style =  [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_MEDIUMGRAY
                            ],
                            'font' => [
                                
                                'bold' => true
                            ]
                        ];
                        $sheet->getStyle('A'.$this->rows.':D'.$this->rows)->applyFromArray(
                            $style
                        );
                    }
                    $sheet->setCellValue('A'.$this->rows, $total['name']);
                    $sheet->setCellValue('B'.$this->rows, $total['total_incentive']);
                    $sheet->setCellValue('C'.$this->rows, $total['total_super_incentive']);
                    $sheet->setCellValue('D'.$this->rows, $total['total_income']);
                    $this->rows++;
                }
    
            }
        }
    }
}
