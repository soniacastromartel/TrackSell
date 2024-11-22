<?php

namespace App\Imports;

use App\Target;
use DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Centre;

class TargetSheetImport implements WithStartRow, ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $year;
    private $isEdit;
    private $centre;
    private $centreId;

    public function __construct($year, $isEdit, $centre)
    {
        $this->year = $year;
        $this->isEdit = $isEdit;
        $this->centre = $centre;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $centreId = Centre::getCentreIdByNameLike($row['centro']);
        // dd($centreId);
        if (!$centreId) {
            throw new \Exception("Centro no encontrado: " . $row['centro']);
        }

        $row['objetivo_venta_cruzada'] = str_replace(',', '', $row['objetivo_venta_cruzada']);
        $row['objetivo_venta_privada'] = str_replace(',', '', $row['objetivo_venta_privada']);
        $row['venta_privada'] = str_replace(',', '', $row['venta_privada']);

        if (!is_numeric($row['mes'])) {
            throw new \Exception("Formato de campo 'mes' inválido.");
        }

        $data = [
            'obj1' => floatval($row['objetivo_venta_cruzada']),
            'obj2' => floatval($row['objetivo_venta_privada']),
            'vd' => floatval($row['venta_privada']),
        ];

        $existingTarget = Target::getTargetByYearMonthAndCentre($this->year, $row['mes'], $centreId);

        if ($existingTarget) {
            $existingTarget->update($data);
            return null;
        } else {
            return new Target([
                'year' => $this->year,
                'month' => $row['mes'],
                'centre_id' => $centreId,
                'obj1' => floatval($row['objetivo_venta_cruzada']),
                'obj2' => floatval($row['objetivo_venta_privada']),
                'vd' => floatval($row['venta_privada']),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'mes' => 'required|integer',
            'centro' => 'required',
            'objetivo_venta_cruzada' => 'required|numeric',
            'objetivo_venta_privada' => 'required|numeric',
            'venta_privada' => 'required|numeric',
        ];
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
