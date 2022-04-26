<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Redirect;

class AccessForbiddenException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {   
        return Redirect::to('/home')->with('error','Error, zona restringida');
    }
}