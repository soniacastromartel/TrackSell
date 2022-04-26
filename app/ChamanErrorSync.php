<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChamanErrorSync extends Model
{   
    protected $table = 'chaman_errores_sync'; 
    protected $fillable = [
        'id',
        'hc',
        'started_date',
        'service',
        'error'
    ];
}
