<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Centre;
use Illuminate\Support\Facades\Log;

class TargetsImport implements WithMultipleSheets
{


    private $year;
    private $isEdit;
    private $centres;
    private $filePath;

    public function __construct($centres, $year, $isEdit, $filePath)
    {

        $this->centres = $centres;
        $this->year = $year;
        $this->isEdit = $isEdit;
        $this->filePath = $filePath;
    }

    public function sheets(): array
    {
        $sheets = [];
        if ($this->isEdit) {
            try {
                $sheets[] = new TargetSheetImport($this->year, true);
            } catch (\Exception $e) {
                Log::error('Error initializing TargetSheetImport for edit mode', [
                    'message' => $e->getMessage(),
                ]);
                throw $e; 
            }
        } else {
            try {
                foreach ($this->centres as $centre) {
                    $sheets[] = new TargetSheetImport($this->year, false);
                }
            } catch (\Exception $e) {
                Log::error('Error initializing sheets for centres', [
                    'message' => $e->getMessage(),
                    'centre' => $centre ?? 'N/A',
                ]);
                throw $e; 
            }
        }    
        return $sheets;
    }
    

}
