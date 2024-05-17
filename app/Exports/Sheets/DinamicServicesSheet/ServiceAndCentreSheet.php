<?php

namespace App\Exports\Sheets\DinamicServicesSheet;

use App\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class ServiceAndCentreSheet implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    private $startDate;
    private $endDate;
    private $selectedService;
    private $selectedCentre;
    private $totalServices;
    private $grandTotal;


    public function __construct($request, $selectedCentre, $selectedService, $totalServices, $grandTotal)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
        $this->selectedService = $selectedService;
        $this->selectedCentre = $selectedCentre;
        $this->totalServices = $totalServices;
        $this->grandTotal = $grandTotal;
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
                'REALIZADOS' => $this->totalServices,
                'NULL7' => '',
                'TOTAL' => $this->grandTotal . '€' ,
            ]]);

            return $data;
        }
    }

    public function headings(): array
    {
        return [
            'SERVICIO', '', '', '', '', '', 'CENTRO', '', 'REALIZADOS','', 'TOTAL',
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
                  //  $event->sheet->mergeCells("I{$row}:J{$row}");
                }
                // $event->sheet->getStyle('I')->getAlignment()->setHorizontal(AlignmenT::HORIZONTAL_LEFT);
                // $event->sheet->getStyle('J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $event->sheet->getStyle("A1:K1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFAEB6BF']
                        //TODO gris
                    ],
                ]);
                $event->sheet->getStyle("A2:K2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF64A8FF']
                        //?Azul
                    ],
                ]);
            },
        ];
    }
}
