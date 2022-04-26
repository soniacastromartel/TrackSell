<?php

namespace App\Imports;

use App\Target;
use DB;
use Maatwebsite\Excel\Row;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Exception;

use Maatwebsite\Excel\Validators\Failure;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TargetImport implements WithStartRow, ToModel,WithHeadingRow, WithValidation //OnEachRow , 
{
    use Importable; 
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
        
        // $exists = Target::where([
        //         'year'      => date('Y'),
        //         'month'     => $row[0],
        //         'centre_id' => $centre->id]); 
       
         
        if (!is_integer($row['mes'])) {
            throw new \Exception("Error formato campo mes");
        }
        $row['objetivo_venta_cruzada'] = str_replace(',','',$row['objetivo_venta_cruzada']);
        $row['objetivo_venta_privada'] = str_replace(',','',$row['objetivo_venta_privada']);
        $row['venta_privada'] = str_replace(',','',$row['venta_privada']);    

        //$row['objetivo_venta_cruzada'] = substr($row['objetivo_venta_cruzada'],0,strpos($row['objetivo_venta_cruzada'],'€')); 
        //$row['objetivo_venta_privada'] = substr($row['objetivo_venta_privada'],0,strpos($row['objetivo_venta_privada'],'€'));
        //$row['venta_privada'] = substr($row['venta_privada'],0,strpos($row['venta_privada'],'€'));

        if (!is_numeric(floatval($row['objetivo_venta_cruzada'])) || empty($row['objetivo_venta_cruzada']) ){
            throw new \Exception("Error formato campo venta cruzada");
        }
        if (!is_numeric(floatval($row['objetivo_venta_privada'])) || empty($row['objetivo_venta_privada']) ){
            throw new \Exception("Error formato campo venta privada");
        }
        if (!is_numeric(floatval($row['venta_privada'])) || empty($row['venta_privada']) ){
            throw new \Exception("Error formato campo venta directa");
        }


        $target =  DB::table('targets')
                ->where('year', date('Y'))
                ->where('month', $row['mes'])
                ->where('centre_id', $centre->id);
            
       
        if (!empty($target->get()->toArray())) {
            $target->update([
                            'obj1' => floatval($row['objetivo_venta_cruzada'])
                            ,'obj2' => floatval($row['objetivo_venta_privada'])
                            ,'vd'   => floatval($row['venta_privada'])
            ]);
            return null;
        } else {
            return new Target([
                'year'       => date('Y')
                ,'month'     => $row['mes']
                ,'centre_id' => $centre->id
                ,'obj1'      => floatval($row['objetivo_venta_cruzada'])
                ,'obj2'      => floatval($row['objetivo_venta_privada'])
                ,'vd'        => floatval($row['venta_privada'])
            ]);

        }
    }

    public function rules(): array
    {
        return [
            'mes'           => 'required:integer',
            'centro'        => 'required:string',
            'objetivo_venta_cruzada' => 'required:numeric',
            'objetivo_venta_privada' => 'required:numeric',
            'venta_privada' => 'required:numeric',
        ];
    }
    // public function onRow(Row $row)
    // {
    //     $rowIndex = $row->getIndex();
    //     $row    = $row->toArray();

    //     $centre = DB::table('centres')->select('id')
    //     ->where('name',$row[1])
    //     ->first();

    //     $group = Target::updateOrCreate([
    //         'year'      => date('Y'),
    //         'month'     => $row[0],
    //         'centre_id' => $centre->id
    //     ]);
    // }
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }    

}
