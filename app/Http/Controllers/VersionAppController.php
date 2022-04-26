<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

    class VersionAppController extends BaseController{

        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
        */
        public function generateVersion()
        {
            try{
                
                $latestVersion = env('VERSION_APP'); 
                $xmlFile = view()->make('version_app')->with(compact('latestVersion'))->render();
                \Illuminate\Support\Facades\Storage::put('public/xml-update-hosting.xml' , $xmlFile);
                
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar centros, contacte con el administrador');
            } 
        }
    }