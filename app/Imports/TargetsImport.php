<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Centre;

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
                $sheets[] = new TargetSheetImport($this->year);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'mensaje' => $e->getMessage()
                ], 400);
            }
        } else {
            try {
                foreach ($this->centres as $centre) {
                    $sheets[] = new TargetSheetImport($this->year);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'mensaje' => $e->getMessage()
                ], 400);
            }

        }
        return $sheets;
    }

}
