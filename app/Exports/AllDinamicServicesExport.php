<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\{
    Axis,
    Chart,
    DataSeries,
    DataSeriesValues,
    Layout,
    Legend,
    PlotArea,
    Title
};
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AllDinamicServicesExport implements FromCollection, WithHeadings, WithEvents
{
    use Exportable;

    protected $request;
    private $startDate;
    private $endDate;
    private $totalServices; // Define totalServices as a class property
    private $grandTotal;    // Define grandTotal as a class property



    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
    }

    public function collection()
    //?Para poder visualizar los datos en las columnas es importante identificar cada columna con un nombre diferente ya que pueden solaparse

    {
        $serviceId = $this->request->input('service_id');
        $centreId = $this->request->input('centre_id');
        if ($this->startDate && $this->endDate) {
            $query = Service::getCountAllServices($serviceId, $this->startDate, $this->endDate);
        } else if (!empty($centreId)) {
            // If centre_id is provided but not the date range
            $query = Service::getCountServicesByCentre($centreId, $this->startDate, $this->endDate);
        } else {
            // Si no hay fechas proporcionadas, maneja esta situación adecuadamente
            $query = Service::getCountAllServices($serviceId);
        }

        $results = $query->get()->sortByDesc('cantidad');
        $totalServices = $results->sum('cantidad');
        $grandTotal = $results->sum(function($item) {
            return $item->price * $item->cantidad;  
        });
         
            $data = $results->map(function ($item) { 
                $extendedData = [
                'SERVICIOS' => $item->service_name,
                'NULL1' => '',
                'NULL2' => '',
                'NULL3' => '',
                'NULL4' => '',
                'NULL5' => '',
                'TOTAL' => $item->cantidad,
            ];

            if (!empty($this->request->input('service_id'))) {
                $extendedData += [
                    'CENTRO' => $item->centre_name,
                    'NULL6' => '',
                    'EMPLEADO' => $item->employee_name,
                    'NULL7' => '',
                    'NULL8' => '',
                    'NULL9' => '',
                    'CATEGORÍA' => $item->employee_category
                ];
            }
            elseif(!empty($this->request->input('centre_id'))) {
                $extendedData += [
                    'PRECIO' => '',
                    'REALIZADOS' => '',
                    'TOTAL' => '',
                  
                ];
            }
        });
        $this->totalServices = $totalServices;
        $this->grandTotal = $grandTotal;

        return $data;
    }


    public function headings(): array
    {
        $headings = ['SERVICIOS', '', '', '', '', '', 'TOTAL'];

        if (!empty($this->request->input('service_id'))) {
            $headings = array_merge($headings, ['SERVICIOS', '', '', '', '', '', 'TOTAL', 'CENTRO', '', 'EMPLEADO', '', '', '', 'CATEGORÍA']);
        } elseif (!empty($this->request->input('centre_id'))) {
            $headings = array_merge($headings, ['SERVICIOS', '', '', '', '', '', 'PRECIO', 'REALIZADOS', 'TOTAL']);
        }
        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if (!empty($this->request->input('service_id'))) {
                    $event->sheet->insertNewRowBefore(1, 1);
                    $event->sheet->insertNewRowBefore(1, 2);
                    $event->sheet->mergeCells("A1:G1");
                    $event->sheet->setCellValue("A1", "Fechas: {$this->startDate} - {$this->endDate}");
                    $event->sheet->setCellValue("A2", "Total: " . $this->totalServices);
                    $event->sheet->setCellValue("A3", "Grand Total: " . $this->grandTotal . '€');
                    $worksheet = $event->sheet->getDelegate();
                    $highestRow = $worksheet->getHighestRow();
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $event->sheet->mergeCells("A{$row}:F{$row}");
                        $event->sheet->mergeCells("H{$row}:I{$row}");
                        $event->sheet->mergeCells("J{$row}:M{$row}");
                        $event->sheet->mergeCells("N{$row}:R{$row}");
                        $event->sheet->mergeCells("S{$row}:T{$row}");
                        $event->sheet->mergeCells("U{$row}:V{$row}");
                    }
                    $event->sheet->getStyle("A1:G1")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFFF00']
                        ],
                    ]);
                    $event->sheet->getStyle("A2:G2")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFFF00']
                        ],
                    ]);
                } else if ($this->startDate && $this->endDate) {
                    $event->sheet->insertNewRowBefore(1, 1);
                    $event->sheet->mergeCells("A1:G1");
                    $event->sheet->setCellValue("A1", "Fechas: {$this->startDate} - {$this->endDate}");
                    $event->sheet->getStyle("A2:G2")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFFF00']
                        ],
                    ]);
                    $event->sheet->getStyle("A1")->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFFF00']
                        ],
                    ]);
                } else {
                    $worksheet = $event->sheet->getDelegate(); // Obtiene el objeto worksheet.
                    $highestRow = $worksheet->getHighestRow(); // Obtiene la última fila con datos.
                    // Combinar las celdas de cada fila para la columna "SERVICIOS".
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $event->sheet->mergeCells("A{$row}:F{$row}");
                        //   $event->sheet->mergeCells("H{$row}:I{$row}");
                        // $event->sheet->mergeCells("J{$row}:M{$row}");
                        // $event->sheet->mergeCells("N{$row}:R{$row}");
                    }

                    // Aplicar estilos a las celdas combinadas
                    $event->sheet->getStyle("A1:C{$highestRow}")->applyFromArray([
                        'font' => ['bold' => true],
                    ]);

                    // No se necesita combinar la columna "TOTAL REALIZADOS", pero aplicamos estilo de negrita.
                    $event->sheet->getStyle("A1:G1")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFFF00']]
                    ]);
                }
            },
        ];
    }
}
  
    // public function charts()
    // {
    //     $itemCount = count($this->servicesCount);
    //     // Define la fuente de los datos para el gráfico
    //     $dataSeriesLabels = [
    //         new DataSeriesValues('String', 'Worksheet!$B$1', null, 1)
    //     ];

    //     $xAxisTickValues = [
    //         new DataSeriesValues('String', 'Worksheet!$A$2:$A$'. ($itemCount + 1),  null, $itemCount)
    //     ];

    //     $dataSeriesValues = [
    //         new DataSeriesValues('Number', 'Worksheet!$B$2:$B$' . ($itemCount + 1), null, $itemCount) 
    //     ];

    //     // Define el gráfico
    //     $series = new DataSeries(
    //         DataSeries::TYPE_BARCHART, // Tipo de gráfico
    //         DataSeries::GROUPING_CLUSTERED, // Agrupamiento
    //         range(0, count($dataSeriesValues) - 1), // Rango
    //         $dataSeriesLabels,
    //         $xAxisTickValues,
    //         $dataSeriesValues
    //     );


    //     $chart = new Chart(
    //         'chart1',
    //         new Title('Venta de Servicios en ICOT'),
    //         new Legend(Legend::POSITION_RIGHT, null, false),
    //         new PlotArea(null, [$series]),
    //         true,
          

    //     );

    //     // Define la posición del gráfico en la hoja
    //     $chart->setTopLeftPosition('A1');
    //     $chart->setBottomRightPosition('AB25');

    //    return [$chart];
 //   }
