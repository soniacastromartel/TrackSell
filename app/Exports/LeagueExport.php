<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use App\Centre;
use App\Services\LeagueService;

use Exception;

class LeagueExport implements
    FromCollection,
    WithStyles,
    WithEvents
{
    use Exportable;

    public function __construct($league = null, $filters=[])
    {

        // $this->leaguePeriod = $filters['state']; 
        $this->league = $league;
        $this->filters = $filters;
        $this->spreadSheet = null;
        $this->rows = 0;
    }

    public function collection()
    {
        return $this->league;
    }


    public function styles(Worksheet $sheet)
    {
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $path = storage_path('templates') . '/export_league.xls';

                $existFile = file_exists($path);
                if (!$existFile) {
                    throw new Exception('error no se ha encontrado fichero de exportación');
                }

                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile($path), Excel::XLS);
                $this->spreadSheet = $event->writer->getDelegate();

                $this->generateContent($this->league, $event);
                // $this->rows = 9;

                return $event->getWriter()->getSheetByIndex(0);
            },
            AfterSheet::class => function (AfterSheet $event) {
                $sheets = $this->spreadSheet->getSheetCount();
                $this->spreadSheet->removeSheetByIndex($sheets - 1);
            }
        ];
    }


    private function generateContent($target, $event)
    {
        $this->printHeader($event->writer->getSheetByIndex(0));

        $this->rows = 9;
        //Vista total de primera pestaña 
        $this->printContent($target, $event->writer->getSheetByIndex(0));
    }

    private function printHeader($sheet)
    {

        $sheet->mergeCells("A1:D1");
        $header = strtoupper($this->filters['state'])."";
        $sheet->setCellValue('A1', "LIGA DE CENTROS " . $header);
        $sheet->setCellValue('B4',$this->filters['fecha']);
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
    private function printContent($league, $sheet)
    {  
        for ($col = 'A'; $col !== 'D'; $col++) {
            $sheet->getColumnDimension($col)
                ->setAutoSize(false);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
        }


        $sheet->getStyle('D')->getNumberFormat()->setFormatCode('### ### ### ##0.00');



        if (!empty($league)) {
            $row = 9;
            foreach ($league as $i => $rankData) {
                $sheet->setCellValue('A' . $row, $rankData['position']);
                $sheet->setCellValue('B' . $row, $rankData['centre']);
                $sheet->setCellValue('C' . $row, $rankData['points']);
                $sheet->setCellValue('D' . $row, $rankData['average']);
                $row++;
            }
        }
    }
}
