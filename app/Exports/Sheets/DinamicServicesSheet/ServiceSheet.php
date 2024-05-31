<?php

namespace App\Exports\Sheets\DinamicServicesSheet;

use App\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ServiceSheet implements FromCollection, WithHeadings, WithEvents
{

    protected $request;
    private $startDate;
    private $endDate;
    private $totalServices;
    private $grandTotal;


    public function __construct($request)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
    }

    public function collection()
    { {
            $serviceId = $this->request->input('service_id');
            $centreId = $this->request->input('centre_id');

            $query = Service::getCountAllServices($serviceId, $centreId, $this->startDate, $this->endDate)
            ->groupBy('employees.name', 'centres.name','service_prices.price')
            ->get()
            ->sortByDesc('cantidad');
            $totalServices = $query->sum('cantidad');
            $grandTotal = $query->sum(function ($item) {
                return $item->price * $item->cantidad;
            });

            $data = $query->map(function ($item) {

                return [
                    'TOTAL' => $item->cantidad,
                    'EMPLEADO' => $item->employee_name,
                    'NULL1' => '',
                    'NULL2' => '',
                    'NULL3' => '',
                    'CATEGORÍA' => $item->category_name

                ];

            });
            $this->totalServices = $totalServices;
            $this->grandTotal = $grandTotal;

            return $data;
        }
    }

    public function headings(): array
    {
        return [
             'TOTAL', 'EMPLEADO', '', '', '', 'CATEGORÍA'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->insertNewRowBefore(1, 1);
                // $event->sheet->insertNewRowBefore(1, 1);
                // $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->insertNewRowBefore(1, 1);
                $fechaTexto = isset($this->startDate) && isset($this->endDate) ? "Fechas: {$this->startDate} / {$this->endDate}" :  "Fechas: Historial completo";
                $event->sheet->setCellValue("A1", $fechaTexto);
                $centreId = $this->request->input('centre_id');
                $centreName = \DB::table('centres')->where('id', $centreId)->value('name'); 
                $event->sheet->setCellValue("A2", "Centro: " . ( $centreName ? $centreName : "Todos los centros"));
                $serviceId = $this->request->input('service_id');
                $serviceName = \DB::table('services')->where('id', $serviceId)->value('name');
                $event->sheet->setCellValue("A3", "Servicio: " . ($serviceName ? $serviceName : "Todos los servicios"));
                // $event->sheet->setCellValue("A4", "Total Realizados: " . $this->totalServices);
                // $event->sheet->setCellValue("A5", "Grand Total: " . $this->grandTotal . '€');
                $worksheet = $event->sheet->getDelegate();
                $highestRow = $worksheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $event->sheet->mergeCells("B{$row}:E{$row}");
                    $event->sheet->mergeCells("F{$row}:I{$row}");
                }
                $event->sheet->getStyle('A')->getAlignment()->setHorizontal(AlignmenT::HORIZONTAL_LEFT);
                $event->sheet->getStyle("A1:I1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFAEB6BF']
                        //TODO gris
                    ],
                ]);
                $event->sheet->getStyle("A2:I2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF52BE80']
                    ],
                ]);
                $event->sheet->getStyle("A3:I3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFCF3CF']
                    ],
                ]);
                // $event->sheet->getStyle("A4:I4")->applyFromArray([
                //     'font' => [
                //         'bold' => true,
                //     ],
                //     'fill' => [
                //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //         'color' => ['argb' => 'FFE74C3C']

                //     ],
                // ]);
                // $event->sheet->getStyle("A5:I5")->applyFromArray([
                //     'font' => [
                //         'bold' => true,
                //     ],
                //     'fill' => [
                //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //         'color' => ['argb' => 'FF27AE60']

                //     ],
                // ]);
                $event->sheet->getStyle("A4:I4")->applyFromArray([
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
