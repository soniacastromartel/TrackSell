<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Illuminate\Support\Facades\Log;

class LogsAppController extends Controller
{
    /**
     * Guarda un registro de informacion sucedida en la app
     * @param $request Contiene los campos:
     *          -type: Representa el tipo de registro [info, warning]
     *          -action: Representa la accion sucesida
     *          -info: Representa informacion adicional
     */
    public function savelogs (Request $request) {
        $type = $request['type'];
        $accion = $request['action'];
        $message = $request['message'];
        $screen = $request ['screen'];
        $channel = $request['channel'];
        Log::channel($channel)->info($type.' - '.$accion.' : '.$message.' => '.$screen); 
        return response([], 200);
    }
}
