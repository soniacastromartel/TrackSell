<?php

namespace App\Http\Controllers;

use Auth;
use App\Centre;
use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RankingsExport;
use App\Services\TargetService; 

class RankingController extends Controller
{
    
    public function __construct()
    {
        $this->user = session()->get('user');
    }  
    
    public function index() {
        $title = 'Rankings';
        $centres = Centre::getActiveCentersWithoutDepartments();

        return view('calculate_rankings', ['title'      => $title
                                             ,'centres'   => $centres
                                             ,'user'      => $this->user
                                             ]
                );
    }

    public function calculateRankings(Request $request) {
        try {

            $params = $request->all();
            $centreName = isset($params['centre'])  ?  $params['centre'] : null;
            $params['centre'] = isset($params['centre'])  ?  Centre::where('name',$params['centre'])->get() : Centre::getCentersWithoutHCT();
            $targetService = new TargetService();
            $params['acumulative'] = false; 
            $exportData =  $targetService->getExportTarget($params, true); 
            
            if (isset($params['monthYear']) && !empty($params['monthYear'])) {
                $year         = substr($params['monthYear'],strpos($params['monthYear'], '/') +1); 
                $currentMonth = substr($params['monthYear'],0,strpos($params['monthYear'], '/'));
            } else {
                $year  = $params['year']; 
                $currentMonth = 1; 
                $params['monthYear'] = $currentMonth .'/'. $params['year']; 
                $params['acumulative'] = true; 
            }

            $this->targetDefined = $targetService->getTarget($params['centre'], $currentMonth, $year);

            if (empty($this->targetDefined)) {
                $cNames = [];
                foreach ($params['centre']->toArray() as $centre) {
                    $cNames[] = is_array($centre) ? $centre['name'] : $centre->name;
                }
                $centresName = implode(',', $cNames);
                throw new \Exception("Error no se ha definido objetivo para el centro: " . $centresName);
            }
            $exportData =  $targetService->getExportTarget($params, true);
            $filters = [
                'centre'       =>  isset($centreName)       ?  $centreName      : 'TODOS',
                'month'        =>  ltrim($currentMonth,"0"),
                'year'         =>  $year,
                'acumulative'  =>  $params['acumulative']
            ];

            ob_end_clean(); 
            ob_start();
            return  Excel::download((new RankingsExport($exportData, $filters)),'ranking.xls');
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ],400); 

            //Redirect::to('/calculateIncentive')->with('error', $e->getMessage());
        }    
    }

}