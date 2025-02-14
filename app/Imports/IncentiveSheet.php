<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use App\Service;
use App\ServicePrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IncentiveSheet implements ToModel, WithHeadingRow, WithStartRow, WithValidation, SkipsEmptyRows, SkipsOnError
{
    use SkipsErrors;

    private $centres;
    private $dataCollection = []; // Store all rows before inserting
    private $userId;

    public function __construct($centres)
    {
        $this->centres = $centres;
        $this->userId = session()->get('user')->id;
    }

    private function prepareData(array $row): array
    {
        return [
            'precio' => floatval(str_replace(',', '', $row['precio'] ?? 0)),
            'incentivo_directo' => floatval(str_replace(',', '', $row['incentivo_directo'] ?? 0)),
            'incentivo_individual_obj1' => floatval(str_replace(',', '', $row['incentivo_individual_obj1'] ?? 0)),
            'incentivo_individual_obj2' => floatval(str_replace(',', '', $row['incentivo_individual_obj2'] ?? 0)),
            'bonus_supervisor_obj1' => floatval(str_replace(',', '', $row['bonus_supervisor_obj1'] ?? 0)),
            'bonus_supervisor_obj2' => floatval(str_replace(',', '', $row['bonus_supervisor_obj2'] ?? 0)),
        ];
    }

    public function model(array $row)
    {
        try {
            if (empty($row) || !isset($row['servicio']) || !isset($row['precio'])) {
                Log::warning('Fila vacía o con datos inválidos:', $row);
                return null;
            }

            $serviceId = Service::findServiceIdByColumn('name', $row['servicio'] ?? '');
            if (!$serviceId) {
                Log::error("Servicio no encontrado: " . ($row['servicio'] ?? 'Desconocido'));
                return;
            }

            // $centres = Service::getCentersForAService($serviceId);
            $centreIds = Service::getCentreIdsForAService($serviceId);
            $centreIdsToCompare = $this->centres->pluck('id')->toArray();

            foreach ($centreIds as $centreId) {
                if (!in_array($centreId, $centreIdsToCompare)) {
                    continue;
                }

                $data = $this->prepareData($row);
                $data['service_id'] = $serviceId;
                $data['centre_id'] = $centreId;
                $data['created_at'] = now();
                $data['updated_at'] = now();

                $this->dataCollection[] = $data; 

                Log::info('Data collected:', $data);
            }

        } catch (\Exception $e) {
            Log::error('Error procesando la fila: ', [
                'row' => $row,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    public function afterImport()
    {
        if (!empty($this->dataCollection)) {
            ServicePrice::importIncentives($this->dataCollection, $this->userId); // Call the method with the collected data
        }
    }
    
    
    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            'precio' => 'required|numeric',
            'incentivo_directo' => 'required|numeric',
            'incentivo_individual_obj1' => 'required|numeric',
            'incentivo_individual_obj2' => 'required|numeric',
            'bonus_supervisor_obj1' => 'required|numeric',
            'bonus_supervisor_obj2' => 'required|numeric',
        ];
    }
}
