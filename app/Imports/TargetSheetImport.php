<?php

namespace App\Imports;

use App\Target;
use DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TargetSheetImport implements WithStartRow, ToModel,WithHeadingRow, WithValidation 
{
    use Importable; 

    public function __construct($year, $onlySales)
    {
        $this->year = $year; 
        $this->onlySales  = $onlySales; 
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $centre = DB::table('centres')->select('id')
                                        ->where('name',$row['centro'])
                                        ->first();
         
        if (!is_integer($row['mes'])) {
            throw new \Exception("Error formato campo mes");
        }

        if ($this->onlySales && count($row) > 4) {
            throw new \Exception("Formato de archivo incorrecto");
        }

        if (!$this->onlySales) {
            $row['objetivo_venta_cruzada'] = str_replace(',','',$row['objetivo_venta_cruzada']);
            $row['objetivo_venta_privada'] = str_replace(',','',$row['objetivo_venta_privada']);

            if (!is_numeric(floatval($row['objetivo_venta_cruzada'])) ){
                throw new \Exception("Error formato campo venta cruzada");
            }
            if (!is_numeric(floatval($row['objetivo_venta_privada'])) ){
                throw new \Exception("Error formato campo venta privada");
            }
        }
        
        $row['venta_privada'] = str_replace(',','',$row['venta_privada']);    

        if (!is_numeric(floatval($row['venta_privada'])) ){
            throw new \Exception("Error formato campo venta directa");
        }

        $target =  DB::table('targets')
                ->where('year', $this->year)
                ->where('month', $row['mes'])
                ->where('centre_id', $centre->id);
            
        if (!empty($target->get()->toArray())) {

            $updateFields['vd']   = floatval($row['venta_privada']); 
            if ($this->onlySales === false ) {
                $updateFields['obj1'] = floatval($row['objetivo_venta_cruzada']);
                $updateFields['obj2'] = floatval($row['objetivo_venta_privada']);
            }

            $target->update($updateFields);
            return null;
        } else {
            if ($this->onlySales) {
                return new Target([
                    'year'       => $this->year
                    ,'month'     => $row['mes']
                    ,'centre_id' => $centre->id
                    ,'vd'        => floatval($row['venta_privada'])
                ]);
            } else {
                return new Target([
                    'year'       => $this->year
                    ,'month'     => $row['mes']
                    ,'centre_id' => $centre->id
                    ,'obj1'      => floatval($row['objetivo_venta_cruzada'])
                    ,'obj2'      => floatval($row['objetivo_venta_privada'])
                    ,'vd'        => floatval($row['venta_privada'])
                ]);
            }
        }
    }

    public function rules(): array
    {
        if ($this->onlySales) {
            return [
                'mes'                    => 'required:integer',
                'centro'                 => 'required:string',
                'venta_privada'          => 'required:numeric',
            ];
        } else {
            return [
                'mes'                    => 'required:integer',
                'centro'                 => 'required:string',
                'objetivo_venta_cruzada' => 'required:numeric',
                'objetivo_venta_privada' => 'required:numeric',
                'venta_privada'          => 'required:numeric',
            ];
        }
    }
    
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }    
}
