<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Centre;

class TargetsImport implements WithMultipleSheets 
{
    
   
    private $year;
    private $isEdit;
    private $centres; 

    public function __construct($centres, $year, $isEdit)
    {
        $this->centres    = $centres; 
        $this->year       = $year; 
        $this->isEdit     = $isEdit;
    }

    public function sheets(): array
    {   
        $sheets = []; 

        if ($this->isEdit) {
            $centreName = $this->getCentreNameFromExcel(); 
            $centre = Centre::getCentreByNameLike($centreName);
            
            if ($centre) {
                $sheets[] = new TargetSheetImport($this->year, $this->isEdit, $centre);
            } else {
                throw new \Exception("El centro con nombre '{$centreName}' no fue encontrado.");
            }
        } else {
            foreach ($this->centres as $centre) {
                $sheets[] = new TargetSheetImport($this->year, $this->isEdit, $centre); 
            }
        }
        return $sheets; 
    }

//TODO
    private function getCentreNameFromExcel()
    {
        $centreName ='Arnao';
        return $centreName;

    }

}
