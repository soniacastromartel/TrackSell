<?php

namespace App\Exports\Sheets\DinamicServicesSheet;

use App\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ServiceAndCentreSheet implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    private $startDate;
    private $endDate;
    private $selectedService;
    private $selectedCentre;
    private $totalServices;


    public function __construct($request, $selectedCentre, $selectedService, $totalServices)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
        $this->selectedService = $selectedService;
        $this->selectedCentre = $selectedCentre;
        $this->totalServices = $totalServices;
    }

    public function collection()
    { {

            $data = collect([[

                'SERVICIO' => $this->selectedService ? $this->selectedService->name : 'SERVICIO SELECCIONADO',
                'NULL1' => '',
                'NULL2' => '',
                'NULL3' => '',
                'NULL4' => '',
                'NULL5' => '',
                'CENTRO' => $this->selectedCentre ? $this->selectedCentre->name : 'CENTRO SELECCIONADO',
                'NULL6' => '',
                'TOTAL' => $this->totalServices,
            ]]);

            return $data;
        }
    }

    public function headings(): array
    {
        return [
            'SERVICIO', '', '', '', '', '', 'CENTRO', '', 'TOTAL'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 1);
                $fechaTexto = isset($this->startDate) && isset($this->endDate) ? "Fechas: {$this->startDate} / {$this->endDate}" :  "Fechas: Historial completo";
                $event->sheet->setCellValue("A1", $fechaTexto);
                $worksheet = $event->sheet->getDelegate();
                $highestRow = $worksheet->getHighestRow();
                for ($row = 1; $row <= $highestRow; $row++) {
                    $event->sheet->mergeCells("A{$row}:F{$row}");
                    $event->sheet->mergeCells("G{$row}:H{$row}");
                }
                $event->sheet->getStyle("A1:I1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'AEB6BF']
                        //TODO gris
                    ],
                ]);
                $event->sheet->getStyle("A2:I2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => '64A8FF']
                        //?Azul
                    ],
                ]);
            },
        ];
    }
}
