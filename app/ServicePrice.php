<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    //
    protected $fillable = [
        'id',
        'service_id',
        'centre_id',
        'price',
        'service_price_direct_incentive',
        'service_price_incentive1',
        'service_price_incentive2',
        'service_price_super_incentive1',
        'service_price_super_incentive2',
        'cancellation_date',
        'user_cancellation_date'
    ];

    //!RELATIONS
     public function service()
    {
        return $this->belongsTo(Service::class);
    }
     
    
    public function price()
    {
        return $this->hasMany('ServicePrice::class');
    }

    //!FUNCTIONS
    // en esta función recogesmos los precios activos de cada servicio
    public function scopeGetPricesActive($query,$serviceId, $centreId)
    {
        return $query->where('service_id', $serviceId)
                     ->where('centre_id', $centreId)
                     ->whereNull('cancellation_date');
    }
}
