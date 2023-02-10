<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class A3Employee extends Model
{
    
    protected $table = 'a3_employees';

    protected $fillable = [
         'employeeCode'
        , 'completeName'
        , 'identifierNumber'
        , 'jobTitleDescription'
        , 'personalemail'
        , 'personalphone'
        , 'dropDate'
        , 'enrolmentDate'
        , 'companyCode'
        , 'workplaceCode'
        , 'workplaceName'
        , 'pdiCentre'
        , 'created_at'
        , 'updated_at'
    ];


    /**
     * Recoge todos los empleados
     */
    public function scopeGetA3Employees()
    {
        $a3employees = DB::table('a3_employees')
            ->orderBy('completeName')->get();
        return $a3employees;
    }
}
