<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use DB; 

class Discount extends Model
{
    protected $table = 'discounts'; 
    protected $fillable = [
        'id',
        'name',
        'type',
        'cancellation_date'
    ];


    public function scopeGetDiscountsByService($query, $service_id = null, $centre_id = null) {
        $query = Discount::select('discounts.name'
                                 ,'discounts.type')
                    ->whereNull('discounts.cancellation_date')
                    ->join('service_prices_discounts', function($join) {
                        $join->on('service_prices_discounts.discount_type','=','discounts.type');
                        $join->whereNull('service_prices_discounts.cancellation_date');
                    })
                    ->join('service_prices', function($join) {
                        $join->on('service_prices.id','=','service_prices_discounts.service_price_id');
                        $join->whereNull('service_prices.cancellation_date');
                    });
        
        $whereFields = "";
        if (!empty($service_id)) {
            $whereFields .=  " service_prices.service_id = " . $service_id;
        }
        if (!empty($centre_id)) {
            if (!empty($whereFields)) {
                $whereFields .= " and "; 
            }
            $whereFields .=  " service_prices.centre_id = " . $centre_id;
        }
        $query = $query
                    ->whereRaw($whereFields); 
        
        // DESCUENTOS SIN CALCULO [DESCUENTO FIDELIZADO]
        $discounts = $query 
                    ->orderBy('discounts.name')->get();

        $otherDiscounts = Discount::select('discounts.name'
                                          ,'discounts.type')
                        ->whereNull('discounts.cancellation_date')
                        ->where('is_calculate', false)
                        ->get();

        $allDiscounts = []; 

        $allDiscounts = array_merge($allDiscounts, $discounts->toArray()); 
        $allDiscounts = array_merge($allDiscounts, $otherDiscounts->toArray()); 

        return collect($allDiscounts); 
    }
            
}
