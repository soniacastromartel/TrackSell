<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChamanSync extends Model
{   
    protected $table = 'chaman_sync'; 
    protected $fillable = [
        'id',
        'pdi_request',
        'SoliPres',
        'State',
        'response'
    ];
}
