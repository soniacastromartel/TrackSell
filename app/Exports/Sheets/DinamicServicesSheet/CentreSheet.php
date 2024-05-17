<?php

namespace App\Exports\Sheets\DinamicServicesSheet;

use App\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CentreSheet implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    private $startDate;
    private $endDate;
    private $totalServices; // Define totalServices as a class property
    private $grandTotal;    // Define grandTotal as a class property


    public function __construct($request)
    {
        $this->request = $request;
        $this->startDate = $request->input('start_date');
        $this->endDate = $request->input('end_date');
    }

    public function collection()
    {
       
    {
        $serviceId = $this->request->input('service_id');
        $centreId = $this->request->input('centre_id');
        
        if (empty($serviceId) && empty($centreId)) {
            $query = Service::getCountAllServices($serviceId,$centreId, $this->startDate, $this->endDate);
        } else if (!empty($centreId)) {
            $query = Service::getCountServicesByCentre($centreId, $this->startDate, $this->endDate);
        } else if (!empty($serviceId)) {
            $query = Service::getCountAllServices($serviceId,$centreId,$this->startDate, $this->endDate);
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
                        'PRECIO' => $item->price . '€',
                        'REALIZADOS'=> $item->total,
                        'NULL6' => '',
                        'TOTAL' => $item->price * $item->total . '€',
                       
                    ];
                
    
            return $extendedData;
        });
        $this->totalServices = $totalServices;
        $this->grandTotal = $grandTotal;

        return $data;
    }

    }

    public function headings(): array
    {
        return [
            'SERVICIOS', '', '', '', '', '','PRECIO', 'REALIZADOS','', 'TOTAL',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->insertNewRowBefore(1, 1);
                $fechaTexto = isset($this->startDate) && isset($this->endDate) ? "Fechas: {$this->startDate} / {$this->endDate}" :  "Fechas: Historial completo";
                $event->sheet->setCellValue("A1", $fechaTexto);
                $centreId = $this->request->input('centre_id');
                $centreName = \DB::table('centres')->where('id', $centreId)->value('name'); 
                $event->sheet->setCellValue("A2", "Centro: " . ( $centreName ? $centreName : "Todos los centros"));
                $worksheet = $event->sheet->getDelegate(); 
                $highestRow = $worksheet->getHighestRow(); 
                for ($row = 1; $row <= $highestRow; $row++) {
                    $event->sheet->mergeCells("A{$row}:F{$row}");
                    $event->sheet->mergeCells("H{$row}:I{$row}");
                }
                $event->sheet->getStyle('H')->getAlignment()->setHorizontal(AlignmenT::HORIZONTAL_LEFT);
                $event->sheet->getStyle('I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $event->sheet->getStyle("A1:J1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'AEB6BF']
                    ],
                ]);
                $event->sheet->getStyle("A2:J2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => '52BE80']
                    ],
                ]);
                $event->sheet->getStyle("A3:J3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => '64A8FF']
                    ],
                ]);

            },
        ];
    }
}
