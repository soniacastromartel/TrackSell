<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'id',
        'year',
        'month',
        'obj1',
        'obj2',
        'centre_id',
        'vd',
        'obj1_done',
        'obj2_done',
        'calc_month'
    ];

}
