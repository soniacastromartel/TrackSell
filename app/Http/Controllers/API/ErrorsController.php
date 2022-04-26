<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController  as BaseController;
use Illuminate\Http\Request;
use DB;
use App\Error;

class ErrorsController extends BaseController{

    public function saveErrors(Request $request){
        try{
                $validData = $request->toArray();
                
                $error = DB::table('errors_app')->insert([
                    'uuid' => $validData['uuid'],
                    'model' => $validData['model'],
                    'version' => $validData['version'],
                    'fabricante' => $validData['fabricante'],
                    'screen' => $validData['screen'],
                    'error' => $validData['ex']
                ]);

                return $this->sendResponse('success',$error);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos'.$e, 500 ); 
        }         
    }
}