<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Exceptions\AccessForbiddenException;
use App\Role; 

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            //$this->user = Auth::user();
            //session()->put('user', $this->user); 
            // if(!str_contains($request, env('API_WORD',""))){
            //    $this->checkPermission(session()->get('user'));             
            // }
           // Log::debug('base: '.$request->username);
            return $next($request);
        });
    }

    // public function checkPermission($userAuthenticated){
    //     if(!empty($userAuthenticated)){
    //       $rol = Role::find($userAuthenticated->rol_id);
    //       $userAuthenticated->levelAccess = $rol['level_id'];
    //       if ( (!empty($userAuthenticated) && $userAuthenticated->levelAccess == 3)  or !empty(( $userAuthenticated->cancellation_date)) ){
    //         throw new AccessForbiddenException('Zona restringida');
    //       }
    //     }
    // }
}
