<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB; 

class RequestChange extends Model
{
    protected $table = 'request_changes'; 
    protected $fillable = [
        'id',
        'start_date',
        'end_date',
        'centre_origin_id',
        'centre_destination_id',
        'employee_id',
        'observations',
        'created_user_id',
        'validated',
        'validated_user_id',
        'cancellation_date'
    ];
  
}
