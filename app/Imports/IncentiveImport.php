<?php

namespace App\Imports;

use App\Service;
use App\ServicePrice;
use App\ServicePriceDiscount;
use DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Centre;

class IncentiveImport implements WithMultipleSheets //OnEachRow , 
{
    use Importable;

    private $centres;

    public function __construct()
    {
        $this->centres = Centre::getCentresActive();
    }

    public function sheets(): array
    {
        $sheets = [];
        $centresGrouped = [
            'LPA-TFE' => $this->centres->whereIn('island', [env('LPA'), env('TFE')]),
            'LNZ-FTV' => $this->centres->whereIn('island', [env('LNZ'), env('FTV')]),
        ];

        try {
            foreach ($centresGrouped as $sheetName => $centres) {
                $sheets[$sheetName] = new IncentiveSheet($centres);
            }

        } catch (\Exception $e) {
            Log::error('Error initializing sheets for centres', [
                'message' => $e->getMessage(),
                'sheet' => $sheetName ?? 'N/A',
            ]);
            throw $e;
        }
        return $sheets;
    }



}
