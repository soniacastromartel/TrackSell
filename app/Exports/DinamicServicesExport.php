<?php

namespace App\Exports;

use App\Employee;
use App\Exports\Sheets\DinamicServicesSheet\AllServiceCentreSheet;
use App\Exports\Sheets\DinamicServicesSheet\CategoryEmployeeSheet;
use App\Exports\Sheets\DinamicServicesSheet\CategoryServiceSheet;
use App\Exports\Sheets\DinamicServicesSheet\CentreSheet;
use App\Exports\Sheets\DinamicServicesSheet\RecommendationsSheet;
use App\Exports\Sheets\DinamicServicesSheet\EmployeeSheet;
use App\Exports\Sheets\DinamicServicesSheet\ServiceAndCentreSheet;
use App\Exports\Sheets\DinamicServicesSheet\ServiceSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Http\Request;

class DinamicServicesExport implements  WithMultipleSheets
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
         
        //!selección de centro y servicio 3 sheets
        if (!empty($this->request->input('service_id')) && !empty($this->request->input('centre_id'))) {
            $sheets[] = new ServiceAndCentreSheet($this->request, $this->selectedCentre, $this->selectedService, $this->totalServices, $this->grandTotal);
            $sheets[] = new CategoryServiceSheet($this->request);
            $sheets[] = new CategoryEmployeeSheet($this->request);
            $sheets[] = new EmployeeSheet($this->request);
        //!selección sólo de servicio 
        }elseif (!empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {
            $sheets[] = new ServiceAndCentreSheet($this->request, $this->selectedCentre, $this->selectedService, $this->totalServices, $this->grandTotal);
            $sheets[] = new CentreSheet($this->request);
            $sheets[] = new RecommendationsSheet($this->request);
            $sheets[] = new CategoryServiceSheet($this->request);
            $sheets[] = new CategoryEmployeeSheet($this->request);
            $sheets[] = new EmployeeSheet($this->request);
        //!selección sólo de centro
        }elseif (empty($this->request->input('service_id')) && !empty($this->request->input('centre_id'))) {
            $sheets[] = new AllServiceCentreSheet($this->request, $this->selectedCentre, $this->selectedService);
            $sheets[] = new CategoryServiceSheet($this->request, $this->selectedCentre);
            $sheets[] = new CategoryEmployeeSheet($this->request, $this->selectedCentre);
            $sheets[] = new EmployeeSheet($this->request);
        //! todos los servicios y centros 
        }elseif (empty($this->request->input('service_id')) && empty($this->request->input('centre_id'))) {
            $sheets[] = new AllServiceCentreSheet($this->request, $this->selectedCentre, $this->selectedService);
            $sheets[] = new CategoryServiceSheet($this->request);
            $sheets[] = new CategoryEmployeeSheet($this->request);
            $sheets[] = new EmployeeSheet($this->request);
        } 
    
        return $sheets;

    }
}
    