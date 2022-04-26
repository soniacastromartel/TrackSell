<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeCentre extends Model
{
    //
    protected $fillable = [
        'id',
        'employee_id',
        'centre_id',
        'cancellation_date'
    ];

     /**
     * The users that belong to the role.
     */
    public function employee()
    {
        return $this->hasMany('EmployeeCentre::class');
    }
}
