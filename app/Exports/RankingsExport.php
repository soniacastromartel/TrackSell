<?php

namespace App\Exports;

use App\Target;
use App\Employee;
use App\Centre;
use DB; 

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use Exception; 
use App\Services\TargetService; 


use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RankingsExport implements FromCollection, WithStyles, WithEvents
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
                
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path('templates/ranking_employee.xls')),Excel::XLS);
                $this->spreadSheet = $event->writer->getDelegate();
                
                $this->generateContentFirstSheet($this->target,$event); 

                return $event->getWriter()->getSheetByIndex(0);
            },
            AfterSheet::class => function(AfterSheet $event){
                $sheets = $this->spreadSheet->getSheetCount(); 
                $this->spreadSheet->removeSheetByIndex($sheets-1);
            }
        ];
    }

    private  function normalizeDataFirstSheet($target){
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
        $tracking = $this->normalizeDataFirstSheet($target); 
        
        $this->printHeaderFirstSheet(array_keys($tracking), $event->writer->getSheetByIndex(0)); 

        $this->rows = 9; 
        //Vista total de primera pestaña 
        $this->printContentFirstSheet($target, $event->writer->getSheetByIndex(0));
    }

    private function printHeaderFirstSheet($centre, $sheet){
        
        $sheet->mergeCells("A1:C1");
        $months = array (1=>'Enero'
                        ,2=>'Febrero'
                        ,3=>'Marzo'
                        ,4=>'Abril'
                        ,5=>'Mayo'
                        ,6=>'Junio'
                        ,7=>'Julio'
                        ,8=>'Agosto'
                        ,9=>'Septiembre'
                        ,10=>'Octubre'
                        ,11=>'Noviembre'
                        ,12=>'Diciembre');
        $monthName = $months[(int)$this->filters['month']]; // March

        $header  = $this->filters['acumulative'] === false ? strtoupper($monthName) ." ": "";
        $header .= strtoupper($this->filters['year']); 
        $sheet->setCellValue('A1',"RANKING " . $header);
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
    private function printContentFirstSheet($target, $sheet){

        $ranking = []; 
        
        for($col = 'A'; $col !== 'E'; $col++) {
            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
        }
        // for($col = 'E'; $col !== 'E'; $col++) {
        // }

         $sheet->getStyle('E')->getNumberFormat()->setFormatCode('### ### ### ##0.00');
         $sheet->getStyle('D')->getNumberFormat()->setFormatCode('### ### ### ##0.00');


        $targetService = new TargetService();
        

        $centre = $this->filters['centre'] == 'TODOS' ? null : $this->filters['centre']; 
        $centres = []; 
        if (empty($centre)) {
            $centres = Centre::getActiveCentersWithoutDepartments(); 
        } else {
            $centres = Centre::whereIn('name',[$centre])->get();
        }

        $ranking = $targetService->getRanking($target,$centres, $this->filters['month'], $this->filters['year'],$this->filters['acumulative']);

        if (!empty($ranking)) {
            $row = 5;
            foreach ($ranking as $i =>$rankData) {
                $sheet->setCellValue('A'.$row,$rankData['position']);
                $sheet->setCellValue('B'.$row,$rankData['employee']);
                $sheet->setCellValue('C'.$row,$rankData['centre']);
                $sheet->setCellValue('D'.$row,$rankData['total_price']);
                $sheet->setCellValue('E'.$row,$rankData['total_incentive']);
                $row++; 
            }
        }
    
    } 

}
