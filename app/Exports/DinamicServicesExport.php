<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DinamicServicesExport
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function download(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Servicio');
        $sheet->setCellValue('B1', 'Total Realizados');

        $row = 2;
        foreach ($this->data as $entry) {
            $sheet->setCellValue('A' . $row, $entry->service_name);
            $sheet->setCellValue('B' . $row, $entry->cantidad);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'services_data.xlsx';

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}
