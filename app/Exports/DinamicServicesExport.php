<?php

namespace App\Exports;

use App\Exports\Sheets\DinamicServicesSheet\CentreSheet as DinamicServicesSheetCentreSheet;
use App\Exports\Sheets\DinamicServicesSheet\ServiceAndCentreSheet as DinamicServicesSheetServiceAndCentreSheet;
use App\Exports\Sheets\DinamicServicesSheet\ServiceSheet as DinamicServicesSheetServiceSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class DinamicServicesExport implements FromCollection, WithHeadings, WithMultipleSheets, WithEvents
{
    use Exportable;

    protected $request;
    protected $selectedCentre;
    protected $selectedService;
    private $startDate;
    private $endDate;
    private $totalServices;
    private $grandTotal;


    public function __construct(Request $request, $selectedCentre, $selectedService, $totalServices,$grandTotal)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
        $this->selectedCentre = $selectedCentre;
        $this->selectedService = $selectedService;
        $this->totalServices = $totalServices;
        $this->grandTotal = $grandTotal;
    }

    public function sheets(): array
    {
        $sheets = [];

        if (!empty($this->request->input('service_id')) && !empty($this->request->input('centre_id'))) {
            $sheets[] = new DinamicServicesSheetServiceAndCentreSheet($this->request, $this->selectedCentre, $this->selectedService, $this->totalServices, $this->grandTotal);
            //?no quiero un sheet con todos los servicios de un centro
            $sheets[] = new DinamicServicesSheetCentreSheet($this->request);
            $sheets[] = new DinamicServicesSheetServiceSheet($this->request);

        }elseif (!empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {
            $sheets[] = new DinamicServicesSheetServiceAndCentreSheet($this->request, $this->selectedCentre, $this->selectedService, $this->totalServices, $this->grandTotal);
            $sheets[] = new DinamicServicesSheetCentreSheet($this->request);
            $sheets[] = new DinamicServicesSheetServiceSheet($this->request);
            
        } else {
            $sheets[] = new DinamicServicesExport($this->request, $this->selectedCentre, $this->selectedService, $this->totalServices,$this->grandTotal);
        }

        return $sheets;
    }

    public function collection()
    //?Para poder visualizar los datos en las columnas es importante identificar cada columna con un nombre diferente ya que pueden solaparse

    {
        $serviceId = $this->request->input('service_id');
        $centreId = $this->request->input('centre_id');

        if (empty($serviceId) && empty($centreId)) {
            $query = Service::getCountAllServices($serviceId, $centreId, $this->startDate, $this->endDate);
        } else if (!empty($centreId)) {
            $query = Service::getCountServicesByCentre($centreId, $this->startDate, $this->endDate);
        } else if (!empty($serviceId)) {
            $query = Service::getCountAllServices($serviceId, $centreId, $this->startDate, $this->endDate);
        }

        $results = $query->get()->sortByDesc('cantidad');


        $totalServices = $results->sum('cantidad');
        $grandTotal = $results->sum(function ($item) {
            return $item->price * $item->cantidad;
        });

        $data = $results->map(function ($item) {

            if (empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {
                $extendedData = [
                    'SERVICIOS' => $item->service_name,
                    'NULL1' => '',
                    'NULL2' => '',
                    'NULL3' => '',
                    'NULL4' => '',
                    'NULL5' => '',
                    'REALIZADOS' => $item->cantidad,
                    'NULL6' => '',
                    'TOTAL'=> $item->price * $item->cantidad . '€',
                ];
            }

            if (!empty($this->request->input('service_id'))) {
                $extendedData = [
                    'CENTRO' => $item->centre_name,
                    'NULL7' => '',
                    'TOTAL' => $item->cantidad,
                    'EMPLEADO' => $item->employee_name,
                    'NULL8' => '',
                    'NULL9' => '',
                    'NULL10' => '',
                    'CATEGORÍA' => $item->employee_category
                ];
            }
            if (!empty($this->request->input('centre_id'))) {
                $extendedData = [
                    'SERVICIOS' => $item->service_name,
                    'NULL10' => '',
                    'NULL11' => '',
                    'NULL12' => '',
                    'NULL13' => '',
                    'NULL14' => '',
                    'PRECIO' => $item->price . '€',
                    'REALIZADOS' => $item->total,
                    'NULL15' => '',
                    'TOTAL' => $item->price * $item->total . '€',

                ];
            }

            return $extendedData;
        });
        $this->totalServices = $totalServices;
        $this->grandTotal = $grandTotal;

        return $data;
    }


    public function headings(): array
    {
        if (empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {
            $heading = ['SERVICIOS', '', '', '', '', '', 'REALIZADOS','','TOTAL'];
            return $heading;
        }

        if (!empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {
            $heading = ['CENTRO', '', 'TOTAL', 'EMPLEADO', '', '', '', 'CATEGORÍA'];
            return $heading;
        }
        if (!empty($this->request->input('centre_id')) && empty($this->request->input('service_id'))) {
            $heading = ['SERVICIOS', '', '', '', '', '', 'PRECIO', 'REALIZADOS', '', 'TOTAL',];
            return $heading;
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                if (empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {

                    //!si no hay servicio ni centro
                    $event->sheet->insertNewRowBefore(1, 1);
                    $fechaTexto = isset($this->startDate) && isset($this->endDate) ? "Fechas: {$this->startDate} / {$this->endDate}" :  "Fechas: Historial completo";
                    $event->sheet->setCellValue("A1", $fechaTexto);
                    $worksheet = $event->sheet->getDelegate(); // Obtiene el objeto worksheet.
                    $highestRow = $worksheet->getHighestRow(); // Obtiene la última fila con datos.
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $event->sheet->mergeCells("A{$row}:F{$row}");
                      //  $event->sheet->mergeCells("G{$row}:H{$row}");
                    }
                    // $event->sheet->getStyle('G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    // $event->sheet->getStyle('H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
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
                            'color' => ['argb' => 'FF64A8FF']
                            //?Azul
                        ],
                    ]);
                }
                //!si hay centro
                if (!empty($this->request->input('centre_id')) && empty($this->request->input('service_id'))) {
                    $event->sheet->insertNewRowBefore(1, 1);
                    $event->sheet->insertNewRowBefore(1, 1);
                    $fechaTexto = isset($this->startDate) && isset($this->endDate) ? "Fechas: {$this->startDate} / {$this->endDate}" :  "Fechas: Historial completo";
                    $event->sheet->setCellValue("A1", $fechaTexto);
                    $centreId = $this->request->input('centre_id');
                    $centreName = \DB::table('centres')->where('id', $centreId)->value('name');
                    $event->sheet->setCellValue("A2", "Centro: " . ($centreName ? $centreName : "Todos los centros"));
                    $worksheet = $event->sheet->getDelegate(); // Obtiene el objeto worksheet.
                    $highestRow = $worksheet->getHighestRow(); // Obtiene la última fila con datos.
                
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $event->sheet->mergeCells("A{$row}:F{$row}");
                    //    $event->sheet->mergeCells("H{$row}:I{$row}");
                    }
                    // $event->sheet->getStyle('H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    // $event->sheet->getStyle('I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
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
                            'color' => ['argb' => 'FF64A8FF']
                        ],
                    ]);
                }

                //! si es servicio
                if (!empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {
                    $event->sheet->insertNewRowBefore(1, 1);
                    $event->sheet->insertNewRowBefore(1, 1);
                    $event->sheet->insertNewRowBefore(1, 1);
                    $event->sheet->insertNewRowBefore(1, 1);
                    $fechaTexto = isset($this->startDate) && isset($this->endDate) ? "Fechas: {$this->startDate} / {$this->endDate}" :  "Fechas: Historial completo";
                    $event->sheet->setCellValue("A1", $fechaTexto);
                    $centreId = $this->request->input('centre_id');
                    $serviceId = $this->request->input('service_id');
                    $serviceName = \DB::table('services')->where('id', $serviceId)->value('name');
                    $event->sheet->setCellValue("A2", "Servicio: " . ($serviceName ? $serviceName : "Todos los servicios"));
                    $event->sheet->setCellValue("A3", "Total Realizados: " . $this->totalServices);
                    $event->sheet->setCellValue("A4", "Grand Total: " . $this->grandTotal . '€');
                    $worksheet = $event->sheet->getDelegate(); // Obtiene el objeto worksheet.
                    $highestRow = $worksheet->getHighestRow(); // Obtiene la última fila con datos.
                    for ($row = 4; $row <= $highestRow; $row++) {
                        $event->sheet->mergeCells("A{$row}:B{$row}");
                        $event->sheet->mergeCells("D{$row}:G{$row}");
                        $event->sheet->mergeCells("H{$row}:L{$row}");
                    }
                    $event->sheet->getStyle("A1:L1")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFAEB6BF']
                            //TODO gris
                        ],
                    ]);
                    $event->sheet->getStyle("A2:L2")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFFCF3CF']
                        ],
                    ]);
                    $event->sheet->getStyle("A3:L3")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFE74C3C']

                        ],
                    ]);
                    $event->sheet->getStyle("A4:L4")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FF27AE60']

                        ],
                    ]);
                    $event->sheet->getStyle("A5:L5")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FF64A8FF']
                            //?Azul
                        ],
                    ]);
                }
            }

        ];
    }
}
