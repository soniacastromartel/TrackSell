<?php

namespace App\Http\Controllers\API;

use App\Centre;
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
}
