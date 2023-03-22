<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Centre;
use App\Exports\DetailedLeagueExport;
use App\Exports\LeagueExport;
use App\Services\LeagueService;
use Maatwebsite\Excel\Facades\Excel;
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

        return view(
            'league_of_centers',
            [
                'title' => $title,
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
        try {
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
                ],
                400
            );
        }
    }
    /**
     * Exporta la clasificacion de la liga de centros
     * 
     * @param $request request Solicitud recibida
     */
    public function exportLeague(Request $request)
    {
        try {
            $params = $request->all();
            $league = new LeagueService();

            if ($params['centre']){
                $exportData= [];
                $results = $league->detailsCentreLeague($request);
                foreach ($results as $i=>$result) {
                    $data[]= [
                        'month' => $result ->month['month'],
                        'average'  => $result ->cv,
                        'points' => $result ->points                    ];
                }
                $exportData= collect($data);

                $fecha = $params['year'];

                $filters = [
                    'fecha' => $fecha,
                    'centre' =>$params['centre'],
                    'state'  =>  $params['state'],
                    'year' => $params['year'],
                ];

                ob_end_clean();
                ob_start();
                return  Excel::download((new DetailedLeagueExport($exportData, $filters)), 'export_league-centre.xls');
            }

                // Clasificacion de liga general
                $exportData = $league->generateLeague($request);
                $fecha = $params['month'] . '/' . $params['year'];
                $filters = [
                    'fecha' => $fecha,
                    'state'  =>  $params['state'],
                    'month' => $params['month'],
                    'year' => $params['year'],
                ];
    
                ob_end_clean();
                ob_start();
                return  Excel::download((new LeagueExport($exportData, $filters)), 'export_league.xls');
                
           


           
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * @params $request Solicitud de datos
     * Detalle de puntuacion obtenida para un centro en concreto
     * 
     * @return Datatable of data
     */
    public function detailsCentreLeague(Request $request)
    {
        try {
            $leagueService = new LeagueService();
            $dataRes = $leagueService->detailsCentreLeague($request);

            if (!empty($dataRes)) {
                return DataTables::of(collect($dataRes))
                    ->addIndexColumn()
                    ->make(true);
            } else {
                return [];
            }
        } catch (Exception $ex) {
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $ex->getMessage(),
                ],
                400
            );
        }
    }
}
