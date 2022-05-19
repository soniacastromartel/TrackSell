<?php

namespace App\Http\Middleware;

use Closure;
use App\Role; 
use App\Providers\RouteServiceProvider;


class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        $userAuthenticated =  session()->get('user'); 
        if(!empty($userAuthenticated->toArray())){
            $rol = Role::find($userAuthenticated->rol_id);
            $userAuthenticated->levelAccess = $rol->level_id;
            if (!empty(( $userAuthenticated->cancellation_date)) or (!empty($userAuthenticated) && $userAuthenticated->levelAccess != 1)   ){
            
                return redirect(RouteServiceProvider::HOME)->with('error','Zona restringida'); 
            }
            
        }
        return $next($request);
    }
}
