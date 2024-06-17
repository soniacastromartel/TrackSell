<?php

namespace App\Exports\Sheets\DinamicServicesSheet;

use App\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AllServiceCentreSheet implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    private $startDate;
    private $endDate;

    public function __construct($request)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
    }

    public function collection()
    {
        $serviceId = $this->request->input('service_id');
        $centreId = $this->request->input('centre_id');


        $query = Service::getCountAllServices($serviceId, $centreId, $this->startDate, $this->endDate)
            ->groupBy('services.name')
            ->get()
            ->map(function ($item) {
                $item->total_price = $item->price * $item->cantidad;
                return $item;
            })->sortByDesc('cantidad');

        $query->sum(function ($item) {
            return $item->price * $item->cantidad;
        });
        $data = $query->map(function ($item) {

            return  [
                'SERVICIOS' => $item->service_name,
                'NULL1' => '',
                'NULL2' => '',
                'NULL3' => '',
                'NULL4' => '',
                'NULL5' => '',
                'REALIZADOS' => $item->cantidad,
                'NULL6' => '',
                'TOTAL' => $item->price * $item->cantidad . '€',

            ];
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'SERVICIOS', '', '', '', '', '', 'REALIZADOS', '', 'TOTAL',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->insertNewRowBefore(1, 1);
                $fechaTexto = isset($this->startDate) && isset($this->endDate) ? "Fechas: {$this->startDate} / {$this->endDate}" :  "Fechas: Historial completo";
                $event->sheet->setCellValue("A1", $fechaTexto);
                $serviceId = $this->request->input('service_id');
                $serviceName = \DB::table('services')->where('id', $serviceId)->value('name');
                $centreId = $this->request->input('centre_id');
                $centreName = \DB::table('centres')->where('id', $centreId)->value('name');
                $event->sheet->setCellValue("A2", "Centro: " . ($centreName ? $centreName : "Todos los centros"));
                $event->sheet->setCellValue("A3", "Servicio: " . ($serviceName ? $serviceName : "Todos los servicios"));
                $worksheet = $event->sheet->getDelegate();
                $highestRow = $worksheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $event->sheet->mergeCells("A{$row}:F{$row}");
                }
                $event->sheet->getStyle("A1:J1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFAEB6BF']
                    ],
                ]);
                $event->sheet->getStyle("A2:J2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF52BE80']
                    ],
                ]);
                $event->sheet->getStyle("A3:J3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFCF3CF']
                    ],
                ]);
          
                $event->sheet->getStyle("A4:J4")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF64A8FF']
                    ],
                ]);
            },
        ];
    }
}
