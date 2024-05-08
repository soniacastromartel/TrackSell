<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\{
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
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Service;
use Illuminate\Http\Request;




class DinamicServicesExport implements FromCollection, WithHeadings, WithCharts
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // Aquí adaptas cómo recuperas los datos en función de los parámetros
        // Por ahora, solo un ejemplo simplificado:
        return Service::query()->get([
            'name as Servicio', // Asume que 'name' es una columna de tu modelo Service
            'cantidad as Total Realizados',
            
        ]);
    }

    public function headings(): array
    {
        return ["Nombre del Servicio", "Fecha de Creación", "Fecha de Actualización"];
    }

    public function charts()
    {
        $labels = ['Servicio 1', 'Servicio 2', 'Servicio 3', 'Servicio 4'];
        $values = [10, 20, 30, 40]; // Valores ejemplo para el gráfico

        // Define la fuente de los datos para el gráfico
        $dataSeriesLabels = [
            new DataSeriesValues('String', 'Worksheet!$B$1', null, 1)
        ];
        $xAxisTickValues = [
            new DataSeriesValues('String', 'Worksheet!$A$2:$A$5', null, count($labels))
        ];
        $dataSeriesValues = [
            new DataSeriesValues('Number', 'Worksheet!$B$2:$B$5', null, count($values))
        ];

        // Define el gráfico
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // Tipo de gráfico
            DataSeries::GROUPING_CLUSTERED, // Agrupamiento
            range(0, count($dataSeriesValues) - 1), // Rango
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Ejemplo de Gráfico');

        $chart = new Chart(
            'chart1',
            $title,
            $legend,
            $plotArea,
            true
        );

        // Define la posición del gráfico en la hoja
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');

        return [$chart];
    }
}