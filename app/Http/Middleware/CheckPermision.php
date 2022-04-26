<?php


namespace App\Http\Middleware;

use Closure;
use App\Providers\RouteServiceProvider;
use Auth;
use App\Role; 

class CheckPermision
{

    public function handle($request, Closure $next)
    {

        $userAuthenticated =  session()->get('user'); 
        if(!empty($userAuthenticated->toArray())){
            $rol = Role::find($userAuthenticated->rol_id);
            $userAuthenticated->levelAccess = $rol->level_id;
            if ( (!empty($userAuthenticated) && $userAuthenticated->levelAccess == 3)  or !empty(( $userAuthenticated->cancellation_date)) ){
            
                return redirect(RouteServiceProvider::HOME)->with('error','Zona restringida'); 
            }
            
        }
        return $next($request);
    
    }
}