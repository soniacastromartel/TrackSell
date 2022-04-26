<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB; 

class TrackingBonus extends Model
{
    protected $table = 'trackings_bonus'; 
    protected $fillable = [
        'id',
        'employee_id',
        'total_income',
        'paid_done',
        'paid_date',
        'paid_user_id',
        'month_year'
    ];

    
}
