<?php

namespace App\Imports;

use App\Target;
use App\Centre;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TargetSheetImport implements WithStartRow, ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $year;
    private $isEdit;
    private $centre;

    public function __construct($year)
    {
        $this->year = $year;
    }

    /**
     * Prepare and transform data for import.
     *
     * @param array $row
     * @return array
     * @throws \Exception
     */
    private function prepareData(array $row): array
    {
        $data = [
            'obj1' => floatval(str_replace(',', '', $row['objetivo_venta_cruzada'] ?? 0)),
            'obj2' => floatval(str_replace(',', '', $row['objetivo_venta_privada'] ?? 0)),
            'vd' => floatval(str_replace(',', '', $row['venta_privada'] ?? 0)),
        ];

        if (!isset($row['mes']) || !is_numeric($row['mes'])) {
            throw new \Exception("Formato de campo 'mes' inválido.");
        }

        $data['mes'] = (int) $row['mes'];

        return $data;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function model(array $row)
    {
        try {
            $centreId = Centre::getCentreIdByNameLike($row['centro'] ?? '');
            if (!$centreId) {
                throw new \Exception("Centro no encontrado: " . ($row['centro'] ?? 'Desconocido'));
            }
            $data = $this->prepareData($row);

            // Use updateOrCreate for efficient database operations
            Target::updateOrCreate(
                [
                    'year' => $this->year,
                    'month' => $data['mes'],
                    'centre_id' => $centreId,
                ],
                [
                    'obj1' => $data['obj1'],
                    'obj2' => $data['obj2'],
                    'vd' => $data['vd'],
                ]
            );
            Log::info('Target processed successfully.', [
                'centre_id' => $centreId,
                'year' => $this->year,
                'month' => $data['mes'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing target:', [
                'row' => $row,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Rethrow the exception to handle it upstream
        }
        // Return null as no model is being returned directly
        return null;
    }

    /**
     * Validation rules for the rows.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'mes' => 'required',
            'centro' => 'required',
            'objetivo_venta_cruzada' => 'required|numeric',
            'objetivo_venta_privada' => 'required|numeric',
        ];
    }

    /**
     * Define the starting row for data.
     *
     * @return int
     */
    public function startRow(): int
    {
        return 2; // Skip the header row
    }
}
