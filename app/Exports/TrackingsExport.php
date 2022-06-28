<?php

namespace App\Exports;

use App\Tracking;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TrackingsExport implements FromCollection, WithStyles, WithEvents
{
    use Exportable; 
    //protected $tracking; 
    /**
    * Optional headers
    */
    // private $headers = [
    //     'Content-Type' => 'text/csv',
    // ];
    public function __construct($tracking = null, $filters)
    {
        
        $this->trackingState = $filters['trackingState']; 
        $this->tracking = reset($tracking);
        $this->filters = $filters;
        $this->spreadSheet = null; 
        
    }

    public function collection(){
        //return Tracking::all();
        return $this->tracking;
    }


    public function styles(Worksheet $sheet)
    {
        $sheet->getHeaderFooter()
        ->setOddHeader('&C&HPlease treat this document as confidential!');
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            'C'  => ['font' => ['size' => 16]],
        ];
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }

    public function registerEvents(): array
    {
        
        return [
            BeforeExport::class => function(BeforeExport $event){
               
               switch ($this->trackingState) {
                   case 'pending':
                       $trackingState = 'PENDIENTES';
                       break;
                    case 'apointment':
                        $trackingState = 'CITADOS';
                        break;
                   case 'service':
                       $trackingState = 'REALIZADOS';
                       break;
                   case 'invoiced':
                       $trackingState = 'FACTURADOS';
                       break;
                   case 'validation':
                       $trackingState = 'VALIDADOS';
                       break;
                   case 'paid':
                       $trackingState = 'PAGADOS';
                       break;
                   case 'cancellation':
                       $trackingState = 'CANCELADOS';
                       break;
                   default:
                        $trackingState = 'TODOS';
                       break;
               }

               $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path('templates/export_tracking.xls')),Excel::XLS);
   
               $event->writer->getSheetByIndex(0);
               $event->writer->getDefaultStyle()
                        ->getFont()
                        ->setName('Arial')
                        ->setSize(10);

               $event->writer->getSheetByIndex(0)->setCellValue('B1',$trackingState);

               $event->writer->getSheetByIndex(0)->setCellValue('B3',$this->filters['employee']);
               $event->writer->getSheetByIndex(0)->setCellValue('B4',$this->filters['centre']);
               $event->writer->getSheetByIndex(0)->setCellValue('B5',$this->filters['service']);
               $event->writer->getSheetByIndex(0)->setCellValue('B6',$this->filters['patient_name']);

               $event->writer->getSheetByIndex(0)->setCellValue('H3',$this->filters['date_from']);
               $event->writer->getSheetByIndex(0)->setCellValue('H4',$this->filters['date_to']);

               $row = 9; 
              
               foreach ($this->tracking as $trackingRow) {
                    $event->writer->getSheetByIndex(0)->setCellValue('A'.$row,$trackingRow->centre_employee);
                    $event->writer->getSheetByIndex(0)->setCellValue('B'.$row,$trackingRow->centre);
                    $event->writer->getSheetByIndex(0)->setCellValue('C'.$row,$trackingRow->employee);
                    $event->writer->getSheetByIndex(0)->setCellValue('D'.$row,"[".$trackingRow->hc."]" . $trackingRow->patient_name);
                    $event->writer->getSheetByIndex(0)->setCellValue('E'.$row,$trackingRow->service);
                    $event->writer->getSheetByIndex(0)->setCellValue('F'.$row,$trackingRow->quantity);
                    $event->writer->getSheetByIndex(0)->setCellValue('G'.$row,$trackingRow->state_date);
                    $event->writer->getSheetByIndex(0)->setCellValue('H'.$row,$trackingRow->cancellation_date);
                    $event->writer->getSheetByIndex(0)->setCellValue('I'.$row,$trackingRow->cancellation_reason);
                    $event->writer->getSheetByIndex(0)->setCellValue('J'.$row,$trackingRow->price * $trackingRow->quantity);
                  
                    $row++; 
               }
            
               $this->spreadSheet = $event->writer->getDelegate(); 
               return $event->getWriter()->getSheetByIndex(0);
            },
            AfterSheet::class => function(AfterSheet $event){
                $sheets = $this->spreadSheet->getSheetCount(); 
                $this->spreadSheet->removeSheetByIndex($sheets-1);
            }
         ];

        /*return [
            // Handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {
                
                $reader = IOFactory::createReader('Xls');
                $spreadSheet = $reader->load('../storage/templates/export.xls');
                
                $spreadSheet->getDefaultStyle()
                        ->getFont()
                        ->setName('Arial')
                        ->setSize(10);

                $event->sheet = $spreadSheet; 
                // last column as letter value (e.g., D)
                //$last_column = Coordinate::stringFromColumnIndex(count($this->results[0]));

                // calculate last row + 1 (total results + header rows + column headings row + new row)
                //$last_row = count($this->results) + 2 + 1 + 1;

                // set up a style array for cell formatting
                
                $style_text_center = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ];

                // at row 1, insert 2 rows
                $event->sheet->insertNewRowBefore(1, 8);
                $event->sheet
                ->setCellValue('B1', 'FACTURADOS'); 


                $event->sheet
                ->setCellValue('A3', 'EMPLEADOS')
                ->setCellValue('A4', 'CENTROS')
                ->setCellValue('A5', 'SERVICIOS')
                ->setCellValue('A6', 'PACIENTES')
                
                ;
                
                $event->sheet
                ->setCellValue('A8', 'CENTRO')
                ->setCellValue('B8', 'EMPLEADO')
                ->setCellValue('C8', '[HC] PACIENTE')
                ->setCellValue('D8', '[SERVICIO');
                

                // merge cells for full-width
                // $event->sheet->mergeCells(sprintf('A1:%s1',$last_column));
                // $event->sheet->mergeCells(sprintf('A2:%s2',$last_column));
                // $event->sheet->mergeCells(sprintf('A%d:%s%d',$last_row,$last_column,$last_row));

                // // assign cell values
                // $event->sheet->setCellValue('A1','Top Triggers Report');
                // $event->sheet->setCellValue('A2','SECURITY CLASSIFICATION - UNCLASSIFIED [Generator: Admin]');
                // $event->sheet->setCellValue(sprintf('A%d',$last_row),'SECURITY CLASSIFICATION - UNCLASSIFIED [Generated: ...]');

                // // assign cell styles
                $event->sheet->getStyle('A1:D8')->applyFromArray($style_text_center);
                // $event->sheet->getStyle(sprintf('A%d',$last_row))->applyFromArray($style_text_center);
            },
        ];*/
    }

    // public function __construct($params, $nullParams,  $notNullParams, $orderBy)
    // {
    //     $this->params = $params;
    //     $this->nullParams = $nullParams;
    //     $this->notNullParams = $notNullParams;
    //     $this->orderBy = $orderBy; 
    // }

    // public function query()
    // {
    //     return Tracking::query()->where($this->params)
    //                     ->whereNull($this->nullParams)                        
    //                     ->whereNotNull($this->notNullParams)
    //                     ->orderBy($this->orderBy)
    //                     ;
    // }

   

    /**
    * @return \Illuminate\Support\Collection
    */
    /*public function collection()
    {
        //return Tracking::all();
        if (empty($this->tracking)){
            $this->tracking = Tracking::all();
        }
        return $this->tracking; 
        //return $this->tracking ? : Tracking::all();
    }*/
}
