<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    //
    protected $table = 'employee_history';

    protected $fillable = [
        'id',
        'employee_id',
        'centre_id',
        'rol_id',
        'cancellation_date',
        'contract_startdate',
        'updated_at',
        'created_at'
    ];

}
