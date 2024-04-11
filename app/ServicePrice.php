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
     
     /**
     * The users that belong to the role.
     */
    
    public function price()
    {
        return $this->hasMany('ServicePrice::class');
    }
}
