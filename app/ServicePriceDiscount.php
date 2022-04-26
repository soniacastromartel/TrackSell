<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicePriceDiscount extends Model
{
    //
    protected $table = 'service_prices_discounts'; 
    protected $fillable = [
        'id',
        'service_price_id',
        'discount_type',
        'price',
        'direct_incentive',
        'incentive1',
        'incentive2',
        'super_incentive1',
        'super_incentive2',
        'cancellation_date',
        'user_cancellation_date'
    ];

}
