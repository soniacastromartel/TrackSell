<?php

namespace App\Imports;

use App\Service;
use App\ServicePrice;
use App\ServicePriceDiscount;
use DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class IncentiveImport implements WithStartRow, ToModel,WithHeadingRow, WithValidation //OnEachRow , 
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

        $service = DB::table('services')->select('id')
        ->where('name',$row['servicio'])
        ->first();
        
        $row['incentivo_directo']         = str_replace(',','',$row['incentivo_directo']);
        $row['incentivo_individual_obj1'] = str_replace(',','',$row['incentivo_individual_obj1']);
        $row['incentivo_individual_obj2'] = str_replace(',','',$row['incentivo_individual_obj2']);
        $row['bonus_supervisor_obj1']     = str_replace(',','',$row['bonus_supervisor_obj1']);
        $row['bonus_supervisor_obj2']     = str_replace(',','',$row['bonus_supervisor_obj2']);
        
        $row['incentivo_directo']         = strpos($row['incentivo_directo'],'€') > 0 ?  substr($row['incentivo_directo'],0,strpos($row['incentivo_directo'],'€')) : $row['incentivo_directo'] ; 
        $row['incentivo_individual_obj1'] = strpos($row['incentivo_individual_obj1'],'€') > 0 ? substr($row['incentivo_individual_obj1'],0,strpos($row['incentivo_individual_obj1'],'€')) : $row['incentivo_individual_obj1']; 
        $row['incentivo_individual_obj2'] = strpos($row['incentivo_individual_obj2'],'€') > 0 ? substr($row['incentivo_individual_obj2'],0,strpos($row['incentivo_individual_obj2'],'€')) : $row['incentivo_individual_obj2'];
        $row['bonus_supervisor_obj1']     = strpos($row['bonus_supervisor_obj1'],'€') > 0 ?  substr($row['bonus_supervisor_obj1'],0,strpos($row['bonus_supervisor_obj1'],'€')) : $row['bonus_supervisor_obj1']; 
        $row['bonus_supervisor_obj2']     = strpos($row['bonus_supervisor_obj2'],'€') > 0 ? substr($row['bonus_supervisor_obj2'],0,strpos($row['bonus_supervisor_obj2'],'€')) : $row['bonus_supervisor_obj2']; 

        if (!is_numeric(floatval($row['incentivo_directo'])) || empty($row['incentivo_directo']) ){
            throw new \Exception("Error formato campo incentivo directo");
        }
        if (!is_numeric(floatval($row['incentivo_individual_obj1'])) || empty($row['incentivo_individual_obj1']) ){
            throw new \Exception("Error formato campo incentivo individual obj1");
        }
        if (!is_numeric(floatval($row['incentivo_individual_obj2'])) || empty($row['incentivo_individual_obj2']) ){
            throw new \Exception("Error formato campo incentivo individual obj2");
        }
        if (!is_numeric(floatval($row['bonus_supervisor_obj1'])) || empty($row['bonus_supervisor_obj1']) ){
            throw new \Exception("Error formato campo bonus supervisor obj1");
        }
        if (!is_numeric(floatval($row['bonus_supervisor_obj2'])) || empty($row['bonus_supervisor_obj2']) ){
            throw new \Exception("Error formato campo bonus supervisor obj2");
        }

        //FIXME... ERROR falta alias_img
        if (empty($service)){
            $service = Service::create(['name' => $row['servicio']]); 
        }

        $servicePrice = ServicePrice::where('centre_id', $centre->id)
                ->where('service_id',$service->id)
                ->whereNull('cancellation_date'); 



        $cancellatedServicePrice = ServicePrice::where('centre_id', $centre->id)
                ->where('service_id',$service->id)
                ->whereNotNull('cancellation_date');


        
        $arrServicePrice = $servicePrice->get()->toArray();    
        $arrCancelServicePrice  = $cancellatedServicePrice->get()->toArray(); 
        
        $serviceCancellated = false; 
        foreach ($arrCancelServicePrice as $key => $cancelService) {
            # code...
            if (date('Y-m-d H:i:s'  , strtotime( '-1 days' ))  <> $cancelService['cancellation_date']) {
                $serviceCancellated = true; 
            }
        }
        

        
            //$oldService = Service::where( ['id' => $service->id] );
            /** Cancelamos servicio */
        if (!empty($arrServicePrice)) {
            $objServicePrice = $arrServicePrice[0];
            $servicePriceId  = $objServicePrice['id'];
        }

        //Obtener service_price id ERROR en collection
        //if ($row['cambia_servicio'] == 'SI') {

        if (!$serviceCancellated && !empty($servicePrice->first())) {
            //$servicePrice->first()->cancellation_date =  date('Y-m-d H:i:s'  , strtotime( '-1 days' )); 
            //$servicePrice->first()->save();
            if (!empty($arrServicePrice)) {
                $servicePrice->first()->update(['cancellation_date' => date('Y-m-d H:i:s'  , strtotime( '-1 days' ))]); 
            }
            
            $servicePrice =  new ServicePrice([
                'price'                             => floatval($row['precio'])
                ,'service_id'                       => $service->id
                ,'centre_id'                        => $centre->id
                ,'service_price_direct_incentive'   => floatval($row['incentivo_directo'])
                ,'service_price_incentive1'         => floatval($row['incentivo_individual_obj1'])
                ,'service_price_incentive2'         => floatval($row['incentivo_individual_obj2'])
                ,'service_price_super_incentive1'   => floatval($row['bonus_supervisor_obj1'])
                ,'service_price_super_incentive2'   => floatval($row['bonus_supervisor_obj2'])
            ]);
            $servicePrice->save();
            $servicePriceId =  $servicePrice->id;
        }
        
        
       // }

        if (!empty($row['descuento'])) {
            
            if (!empty($arrServicePrice)) {
                
                $servicePriceDiscount =  DB::table('service_prices_discounts')
                ->where('service_price_id',$arrServicePrice[0]['id'] )
                ->where('discount_type', $row['descuento'])
                ->whereNull('cancellation_date');

                if ( !empty($servicePriceDiscount->get()->toArray()) ) {
                    $servicePriceDiscount->update(['cancellation_date' => date('Y-m-d H:i:s'  , strtotime( '-1 days' ))]); 
                }
            }
        
            return new ServicePriceDiscount([
                'service_price_id'    => $servicePriceId,
                'discount_type'       => $row['descuento'],
                'price'               => floatval($row['descuento_precio']),
                'direct_incentive'    => floatval($row['descuento_incentivo_directo']),
                'incentive1'          => floatval($row['descuento_incentivo_individual_obj1']),
                'incentive2'          => floatval($row['descuento_incentivo_individual_obj2']),
                'super_incentive1'    => floatval($row['descuento_bonus_supervisor_obj1']),
                'super_incentive2'    => floatval($row['descuento_bonus_supervisor_obj2']),
            ]);

            // PERMITIR SERVICE_PRICE SI DISCOUNT_TYPE = DISCOUNT.TYPE y no cancelado

        } else {
            $servicePrice->update(['cancellation_date' => date('Y-m-d H:i:s'  , strtotime( '-1 days' ))]); 
            $servicePrice =  new ServicePrice([
                'price'                             => floatval($row['precio'])
                ,'service_id'                       => $service->id
                ,'centre_id'                        => $centre->id
                ,'service_price_direct_incentive'   => floatval($row['incentivo_directo'])
                ,'service_price_incentive1'         => floatval($row['incentivo_individual_obj1'])
                ,'service_price_incentive2'         => floatval($row['incentivo_individual_obj2'])
                ,'service_price_super_incentive1'   => floatval($row['bonus_supervisor_obj1'])
                ,'service_price_super_incentive2'   => floatval($row['bonus_supervisor_obj2'])
            ]);
    
            $servicePrice->save();

            return $servicePrice;
        }
       
    }

    public function rules(): array
    {
        return [
            'centro'                    => 'required:string',
            'servicio'                  => 'required:string',
            'precio'                    => 'required:numeric',
            'incentivo_directo'         => 'required:numeric',
            'incentivo_individual_obj1' => 'required:numeric',
            'incentivo_individual_obj2' => 'required:numeric',
            'bonus_supervisor_obj1'     => 'required:numeric',
            'bonus_supervisor_obj2'     => 'required:numeric'
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
