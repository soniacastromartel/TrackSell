<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\Sheets\IncentivesPerBusinessSheet; 

class TrackingsValidateExport implements  WithMultipleSheets//,  WithStyles
{
    use Exportable; 
    public function __construct($tracking = null, $filters)
    {
        $this->tracking = empty($tracking->toArray()) ? [] : $tracking ;
        $this->filters = $filters;
        $this->spreadSheet = null; 
        
    }

    public function collection(){
        return $this->tracking;
    }
    /**
     * @return array
     */
    public function sheets(): array
    {
        $this->sheets = [];
        if (!empty($this->filters)) {
            foreach ($this->filters as $codBusiness) {
                $this->sheets[] = new IncentivesPerBusinessSheet(collect($this->tracking[$codBusiness]), $codBusiness);
            }
        } else {
            $this->sheets[] = new IncentivesPerBusinessSheet(collect([]), null);
        }

        return $this->sheets;
    }
}