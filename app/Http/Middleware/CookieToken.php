<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;


class CookieToken
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
        $token = $request->header('Authorization'); // Assuming the token is passed in the 'Authorization' header
        
        // Check if the token matches the token stored in cookies
        // $cookieToken = Cookie::get('token');        
        
        // Check token expiration
        if (!$token) {
            return route('login');
        }
        // if ($token !== 'Bearer ' . $cookieToken) {
        //     return route('login');
        // }

        // return $next($request);
    }
}
