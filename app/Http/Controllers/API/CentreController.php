<?php

namespace App\Http\Controllers\API;

use App\Centre;
use App\Department;
use App\Http\Controllers\API\BaseController as BaseController;

class CentreController extends BaseController{


    public function getCenters()
    {
        try{
            $centers = Centre::select('id', 'name','image')
                                ->whereNull('cancellation_date')
                                ->orderBy('name')->get();

            return $this->sendResponse($centers, '');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500 ); 
        } 
    }
    public function getDepartments()
    {
        try{
            $departments = Department::all();
            return $this->sendResponse($departments, '');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500 ); 
        } 
    }

    public function getCentersByService($serviceId)
    {
        try{
            $centers = Centre::getCentersByServiceId($serviceId);
            return $centers;
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500 ); 
        } 
    }
}
