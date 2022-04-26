<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TargetsImport implements WithMultipleSheets 
{
   
    public function __construct($centres, $year, $onlySales)
    {
        $this->centres    = $centres; 
        $this->year       = $year; 
        $this->onlySales  = $onlySales; 
    }

    public function sheets(): array
    {   
        $sheets = []; 
        foreach ($this->centres as $centre) {
            $sheets[] = new TargetSheetImport($this->year, $this->onlySales); 
            
        }
        return $sheets; 
    }
}
