<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Excel;


class ServicesIncentivesExport implements FromCollection, WithStyles, WithEvents
{
    use Exportable; 

    public function __construct($services = null, $filters=[])
    {
        $this->services = $services;
        $this->filters = $filters; 
        $this->spreadSheet = null; 
        $this->rows = 0;
    }

     /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        return $this->services;
    }

    public function styles(Worksheet $sheet)
    {
        
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function(BeforeExport $event){
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path('templates/export_services.xls')),Excel::XLS);
                $this->spreadSheet = $event->writer->getDelegate();

                $this->generateContent($this->services,$event); 

                return $event->getWriter()->getSheetByIndex(0);
            },
            AfterSheet::class => function(AfterSheet $event){
                $sheets = $this->spreadSheet->getSheetCount(); 
                $this->spreadSheet->removeSheetByIndex($sheets-1);
            }
        ];
    }

    private function generateContent($services,$event){
        
        $this->rows = 2; 

        $this->printContentFirstSheet($services, $event->writer->getSheetByIndex(0));
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
    private function printContentFirstSheet($services, $sheet){

        $aligmentLeft = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
        
        for($col = 'A'; $col !== 'O'; $col++) {
            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
        }

        foreach ($services as $service) {
            $sheet->setCellValue('A'.$this->rows,$service->center);
            $sheet->setCellValue('B'.$this->rows,$service->service);
            $sheet->setCellValue('C'.$this->rows,$service->service_price);
            $sheet->setCellValue('D'.$this->rows,$service->service_direct_incentive);
            $sheet->setCellValue('E'.$this->rows,$service->service_incentive1);
            $sheet->setCellValue('F'.$this->rows,$service->service_incentive2);
            $sheet->setCellValue('G'.$this->rows,$service->service_super_incentive1);
            $sheet->setCellValue('H'.$this->rows,$service->service_super_incentive2);
            $sheet->setCellValue('I'.$this->rows,$service->discount ? $service->discount : 'SIN DESCUENTO');
            $sheet->setCellValue('J'.$this->rows,$service->discount_price ? $service->discount_price : 0);
            $sheet->setCellValue('K'.$this->rows,$service->discount_direct_incentive ? $service->discount_direct_incentive : 0);
            $sheet->setCellValue('L'.$this->rows,$service->discount_incentive1 ? $service->discount_incentive1 : 0);
            $sheet->setCellValue('M'.$this->rows,$service->discount_incentive2 ? $service->discount_incentive2 : 0);
            $sheet->setCellValue('N'.$this->rows,$service->discount_super_incentive1  ? $service->discount_super_incentive1 : 0);
            $sheet->setCellValue('O'.$this->rows,$service->discount_super_incentive2  ? $service->discount_super_incentive2 : 0);
            $this->rows++;
        }    
    }

}