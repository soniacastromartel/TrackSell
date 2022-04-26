<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Centre;
use App\Services\LeagueService;
use DataTables;

class LeagueController extends Controller
{
    public function __construct()
    {
        $this->user = session()->get('user');
    }  
    
    public function index() 
    {
        $title = 'Liga de Centros';
        $centres = Centre::getCentersWithoutHCT();

        return view ('league_of_centers', [  'title' => $title,
                                            'centres' => $centres,
                                            'user'  => $this->user
                                            ]
        );
    }

    /**
     * Genera la clasificacion de la liga de centros
     * 
     * @param $request request Solicitud recibida
     */
    public function generateLeague(Request $request)
    {
        try{
            $leagueService = new LeagueService();
            $result = $leagueService->generateLeague($request);

            return DataTables::of(collect($result))
                ->addIndexColumn()
                ->make(true);
        } catch (Exception $e) {            
            return response()->json(
                [
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ], 400 ); 
        }
    }

    /**
     * @params $request Solicitud de datos
     * Detalle de puntuacion obtenida para un centro en concreto
     * 
     * @return Datatable of data
     */
    public function detailsCentreLeague(Request $request )
    {
        try{
            $leagueService = new LeagueService();
            $dataRes = $leagueService->detailsCentreLeague($request);

            if (!empty($dataRes[0]) ) {
                return DataTables::of(collect($dataRes))
                    ->addIndexColumn()
                    ->make(true);
            } else {
                return [];
            }
        } catch(Exception $ex){
            return response()->json(
                [
                'success' => 'false',
                'errors'  => $ex->getMessage(), 
            ], 400 );
        }
        
    }
}
