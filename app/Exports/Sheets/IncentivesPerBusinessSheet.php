<?php

namespace App\Exports\Sheets;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use App\A3Centre;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class IncentivesPerBusinessSheet extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder  implements FromCollection, WithEvents
                                        , WithCustomStartCell, WithHeadings
                                        ,WithMapping, WithTitle, WithStyles, ShouldAutoSize
                                        ,WithCustomValueBinder
{   
    use Exportable; 
    
    public function __construct($tracking = null, $codBusiness)
    {
        $this->tracking = $tracking;
        $this->codBusiness = $codBusiness;
    }

    public function collection(){
        return $this->tracking;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
            '',
            'Importe (Incentivos PDI)'
        ];
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function map($row): array
    {
        return [
            $row['cod_employee'],
            $row['name'],
            '',
           str_replace('.',',', $row['total_income'])
        ];
    }

    public function title(): string
    {
        return 'Empresa (' . substr($this->codBusiness, 0, strpos($this->codBusiness, "-")).')';
    }

    public function coloringCells($color,$cells, $sheet){
      return $sheet->getStyle($cells)->applyFromArray(
        array (
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('rgb' => $color)
        ])
            );
    }

    public function styles(Worksheet $sheet)
    {
       


        $aligmentRight = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
         $sheet->getStyle('C2:D2')->applyFromArray(array (
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
        ]));
         $sheet->getStyle('A5:B5')->applyFromArray(array (
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
                
        ]));
         $sheet->getStyle('A7:D7')->applyFromArray(array (
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
                
        ]));
         $sheet->getStyle('A8:D8')->applyFromArray(array (
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
              
        ]));

        $sheet->getStyle('A:J')->getAlignment()->setHorizontal($aligmentRight);
            return [
                // Style the first row as bold text.
                7    => ['font' => ['bold' => true]],
                8    => ['font' => ['bold' => true]],
                'C2' => ['font' => ['bold' => true]], 
                'C5' => ['font' => ['bold' => true]],
                'B2' => ['font' => ['bold' => true]], 
                'B5' => ['font' => ['bold' => true]],
                'D2' => ['font' => ['bold' => true], 'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ]],
                'D5' => ['font' => ['bold' => true], 'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ]],
            ];
    }
    
    public static function beforeSheet( $codBusiness, $tracking, BeforeSheet $event)
    {
        $sheet = $event->sheet; 
        $sheet->setCellValue('B2','EMPRES');
        $sheet->setCellValue('C2','A');
        $sheet->setCellValue('D2',$codBusiness);
        $sheet->setCellValue('B5','FECH');
        $sheet->setCellValue('C5','A');
        $sheet->setCellValue('D5',date('d/m/Y'));

        $sheet->mergeCells("A7:C7");
        $sheet->setCellValue('A7','TRABAJADORES');

    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event){
                $this->beforeSheet($this->codBusiness,$this->tracking->toArray(), $event);
            }
         ];
    }
}
?>