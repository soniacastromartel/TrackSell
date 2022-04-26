<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\LeagueService;
use Illuminate\Http\Request;

class LeagueCentresController extends Controller
{
    /**
     * Show clasification league
     * 
     * @param $request Solicitud a procesar
     * 
     * @return $clasificationFinal Clasificacion de liga a mostrar
     */
    public function getClasification(Request $request)
    {
        $league = new LeagueService();
        $params = $request->all();
        $clasificationFinal = [];
        if (!$params['centre']) {
            // Clasificacion de liga general
            $clasificationFinal = $league->generateLeague($request);
        } else {
            // Detalle por centro en la clasificacion de liga
            $clasificationFinal = $league->detailsCentreLeague($request);
        }
        return $clasificationFinal;
    }
}
