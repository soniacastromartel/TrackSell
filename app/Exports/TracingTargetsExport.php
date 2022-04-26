<?php

namespace App\Exports;

use App\Target;
use App\Employee;
use App\Centre;
use DB; 
//use App\Services\TargetService; 

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use Exception; 

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TracingTargetsExport implements FromCollection, WithStyles, WithEvents
{
    use Exportable; 
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
                
                $existFile = file_exists(storage_path('templates').'/tracing_targets-'.$this->filters['year'].'.xls'); 
                if (!$existFile) {
                    throw new Exception ( 'error no se ha encontrado fichero de exportación'); 
                }
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path('templates/tracing_targets-'.$this->filters['year'].'.xls')),Excel::XLS);
                $this->spreadSheet = $event->writer->getDelegate();
                
                $this->generateContent($this->target,$event); 

                return $event->getWriter()->getSheetByIndex(0);
            },
            AfterSheet::class => function(AfterSheet $event){
                $sheets = $this->spreadSheet->getSheetCount(); 
                $this->spreadSheet->removeSheetByIndex($sheets-1);
            }
        ];
    }

    
    private function generateContent($target,$event){
        
        $this->rows = 5; 

        //Vista total de primera pestaña 
        foreach (array_keys($target->toArray()) as $employeeCenter) {
            $this->printContent($target,$employeeCenter, $event->writer->getSheetByIndex(0));
        }
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
    private function printContent($target,$centre, $sheet){

        $filaCont = $this->rows+1; 
        foreach ($target[$centre] as $i =>$targetRow) {
            
            $sheet->setCellValue('D3' , $this->filters['year']);
            $sheet->setCellValue('B'.($this->rows) , $centre);
            $range = range('E', 'P');
            if (empty($target[$centre][$i])) {
                $this->rows = $filaCont;  
            }
            foreach($target[$centre][$i] as $m=> $targetMonth){
                $colMonth = $range[$m-1];
                $this->rows = $filaCont;  

                $styleOk =  [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => array('rgb' => '39843c')
                    ]
                ];
                $styleError =  [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => array('rgb' => 'f33527')
                    ]
                ];
                
                if ($targetMonth['vc'] >= $targetMonth['obj_vc']) {
                    $sheet->getStyle($colMonth.$this->rows)->applyFromArray(
                        $styleOk
                    );
                }else {
                    $sheet->getStyle($colMonth.$this->rows)->applyFromArray(
                        $styleError
                    );
                }
                $sheet->setCellValue($colMonth.$this->rows , $targetMonth['obj_vc']);

                if ($targetMonth['vp'] >= $targetMonth['obj_vp']) {
                    $sheet->getStyle($colMonth.($this->rows+1))->applyFromArray(
                        $styleOk
                    );
                }else {
                    $sheet->getStyle($colMonth.($this->rows+1))->applyFromArray(
                        $styleError
                    );
                }

                $sheet->setCellValue($colMonth.($this->rows+1) , $targetMonth['obj_vp']);
                $sheet->setCellValue($colMonth.($this->rows+2) , $targetMonth['vc']);
                $sheet->setCellValue($colMonth.($this->rows+3) , $targetMonth['vp']);  
                $sheet->setCellValue($colMonth.($this->rows+4) , $targetMonth['cont_employees']); 
                $sheet->setCellValue($colMonth.($this->rows+5) , $targetMonth['salesPerEmployee']);  
              
                // Coeficiente de venta
                $dif_vp = $targetMonth['vp'] - $targetMonth['obj_vp'];
                $dif_vc = $targetMonth['vc']- $targetMonth['obj_vc'];
                $sum_sale = $targetMonth['obj_vc'] + $targetMonth['obj_vp'];
                $coeficiente = $sum_sale > 0 ?  floatval(($dif_vp + $dif_vc) / $sum_sale) : 0;  
                $targetMonth['saleCoefficient'] = round($coeficiente,2);
                $sheet->setCellValue($colMonth.($this->rows+6) , $targetMonth['saleCoefficient']);
                $sheet->setCellValue($colMonth.($this->rows+7) , $targetMonth['total_incentive']); 
                $sheet->setCellValue($colMonth.($this->rows+8) , $targetMonth['incentive_percent']); 
            }
            
            $this->rows += 11;
            $filaCont = $this->rows+1; 

            
        }
    } 


}
