<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Auth; 
use DB;
use App\Centre;
use App\Employee;
use App\EmployeeHistory;
use App\Role;
use App\Target;
use App\Services\TargetService; 
use DataTables;
use App\A3Centre; 

class HomeController extends Controller
{
    public $title; 
    private $trackingCentre;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->title = config('titles.' . Route::currentRouteName());
        $this->title = __('pages.titles.' . $this->title);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()

    {   
        $this->user = session()->get('user');
        $rol = Role::find($this->user->rol_id);
        $this->user->levelAccess = $rol['level_id'];
        $employee = Employee::select('employees.name', 'centres.name as centre',  'centres.id as centre_id', 'employees.rol_id')
            ->leftJoin('employee_history', 'employees.id', '=', 'employee_history.employee_id')
            ->leftJoin('centres', 'centres.id', '=', 'employee_history.centre_id')
            ->where('employees.id', '=', $this->user->id)
            ->whereNull('employee_history.cancellation_date')
            ->orderBy('employee_history.created_at', 'desc')
            ->first(); 
        $centres = Centre::getCentresActive();

        if (empty($employee)) {
            $employee->employee_name = '';
            $employee->centre = '';
        } else {
            $currentMonth = date('m'); 
            $currentYear = date('Y'); 
            $currentMonth = ltrim($currentMonth, '0'); 
            $target = Target::select(
                'targets.vd',
                'targets.obj1',
                'targets.obj2'
            )
                ->where('targets.centre_id', '=',  $employee->centre_id) 
                ->where('targets.month', '=', $currentMonth)
                ->where('targets.year', '=', $currentYear)
                ->get();
            
            $vp = ['value' => 0, 'target' => 0]; 
            if (isset($target[0])) {
                $vp = ['value' => $target[0]->vd, 'target' => $target[0]->obj2]; 
            }
            

            $params['centre']    = $centres;
            $params['monthYear'] = $currentMonth . '/' . $currentYear; 
            $targetService = new TargetService();
            
            \Session::forget('trackingCentre'); // TODO: TrackingCentre revisar 

            $this->trackingCentre = \Session::get('trackingCentre');
            if (empty($this->trackingCentre)) {
                $this->trackingCentre = $targetService->getExportTarget($params);
                \Session::forget('trackingCentre');
                \Session::push('trackingCentre', $this->trackingCentre); 
            } else {
                $this->trackingCentre = \Session::get('trackingCentre')[0];
            }

            $tracking = []; 
            foreach ($this->trackingCentre as $trackingCentreRow) {
                if (!isset($tracking[$trackingCentreRow->centre_employee])) {
                    $tracking[$trackingCentreRow->centre_employee] = []; 
                }
                $tracking[$trackingCentreRow->centre_employee] = []; 
            }
            foreach ($this->trackingCentre as $trackingCentreRow) {
                $tracking[$trackingCentreRow->centre_employee][] = $trackingCentreRow; 
            }
            
            //Get VC from center and month
            //$tracking = Target::select('sum')
            $vcTotal =  $targetService->getVC($params['centre'], $tracking ); 
            $vc = ['value' => 0, 'target' => 0]; 
            if (isset($target[0])) {
                $vc = ['value' => $vcTotal, 'target' => $target[0]->obj1]; 
            }
        }
        //FIXME--- Usuario de varios centros, coger ultimo
        return view('home', ['title'       => 'Inicio'
            , 'user'      => $this->user
            , 'employee'  => $employee
            , 'vp'        => isset($vp) ? $vp : ['value' =>  0 , 'target' => 0]
            , 'vc'        => isset($vc) ? $vc : ['value' => 0 , 'target'  => 0]
            , 'centres'   => $centres
            ]
        ); 
    }

    public function profile() 
    {
        $employee = Auth::user();
        $rol_id = $employee['rol_id'];
        $rol_id = 1; 
        session()->reflash();
        switch ($rol_id) {
        case 1:
            // code...
            //$view = adminProfile();
            return redirect()->route('admin.profile');
            break;
        case 2:
            // code...
            return redirect()->route('employee.supervisorProfile');
            break;
        default:
            return redirect()->route('employee.profile');
            break;
        }
         //return $view; 
    }

    public function viewProfile() 
    {
        
        $centres = Centre::all(); 
        $roles   = Role::all(); 

        $employee = session()->get('user'); 
        $title = $this->title; 
        
        return view('profile')->with(compact('title', 'centres', 'roles', 'employee'));

    }

    public function editProfile(Request $request, $employee_id) 
    {
        $params = $request->all();
        $motives = explode(',',env('EXCLUDE_RANKINGS'));
        $indexMotive = (int) $params['excludingR'];
        $params['excludingR'] != 'null' 
            ? $params['excludingR'] = $motives[$indexMotive]
            : $params['excludingR'] = null;
        
        DB::transaction(function () use ($params, $employee_id) {
            $employee = Employee::find($employee_id); 

            $employeeHistory = DB::table('employee_history') ->where(['employee_id' => $employee->id,
                                                                    'cancellation_date' => null
                ]
            );

            $cancelDate = (isset($params['date_before']) ? $params['date_before'] : date('Y-m-d')) .' 00:00:00';
            $newDate = (isset($params['date_new']) ? $params['date_new'] : date('Y-m-d')) .' 00:00:00'; 
            $employeeHistory->update(['cancellation_date' =>  $cancelDate ]);

            if (isset($params['rol_id'])) {
                $rolId =$params['rol_id']; 
            } else if (isset($params['rol_id_hidden'])) {
                $rolId =$params['rol_id_hidden'];
            } else {
                $rolId =$employee->rol_id;  
            }

            $manualCentre = false; 
            if (isset($params['manual_centre'])) {
                $manualCentre = true; 
                $a3Centre = A3Centre::where('centre_id', '=' , $params['centre_id'])->first(); 
                $codBusiness = null; 
                if (!empty($a3Centre->toArray())) {
                    $codBusiness = $a3Centre->code_business; 
                }
            }
             //TODO.. manual_centre : true (cancelamos los anteriores employee_history)
            $arrayEmployeeHistory = $employeeHistory->get()->toArray(); 
            $created = false;
            foreach ($arrayEmployeeHistory as $eh) {
                if ($eh->centre_id == $params['centre_id']) {
                    $created = true; 
                }
            }
            if ($created === false) {
                EmployeeHistory::create(['employee_id'  => $employee_id
                                        ,'centre_id'   => $params['centre_id']
                                        ,'rol_id'      => $rolId
                                        ,'excludeRanking'=> $params['excludingR']
                                        ,'created_at'  => $newDate
                                        ,'updated_at'  => $newDate
                                        ,'exclude'
                ]);
            }
            $updateParams = ['centre_id'         => isset($params['centre_id']) ? $params['centre_id'] : $employee->centre_id
                                ,'rol_id'           => $rolId
                                ,'force_centre_id'  => $manualCentre
                                ,'excludeRanking'=> $params['excludingR']
                                ,'updated_at'       => $newDate
            ];
            if (!empty($codBusiness)) {
                $updateParams = array_merge($updateParams, ['cod_business' => $codBusiness]); 
            }
            $employee->update($updateParams);
        });

        return redirect()->action('HomeController@profile')
        
            ->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * 
     * 
     * Funcion que obtiene el ranking mensual
     */
    public function getSales(Request $request) {

        try{
            $ranking= []; 
            if ($request->ajax()) {

                $beginMonth   = 1; 

                $monthYear = $request->get('monthYear');
                $currentMonth = empty($monthYear) ? date('m') : substr($monthYear, 0, strpos($monthYear, '/')); 
                $currentYear  = empty($monthYear) ? date('Y') : substr($monthYear, strpos($monthYear, '/') +1);
                
                if (!empty($request->get('centre'))) {
                    $centres = Centre::where('id', $request->get('centre'))->get(); 
                } else {
                    $centres = Centre::getCentersWithoutHCT();
                }
                $params['acumulative'] =  false;
                if ($request->get('type') == 'monthly') {
                    $beginMonth   = $currentMonth; 
                } else {
                    $params['acumulative'] =  true;
                }
                $params['centre'] =  $centres;
                $params['monthYear'] = $beginMonth . '/' . $currentYear; 
                    
                $targetService = new TargetService();
                
                if ($request->type == 'anual') {
                    $this->trackingCentre = \Session::get('trackingTotal')[0]; 
                }
                if ($request->type == 'monthly') {
                    \Session::forget('trackingCentre');
                    $this->trackingCentre = $targetService->getExportTarget($params, true);
                    \Session::push('trackingCentre', $this->trackingCentre);
                } else {
                    \Session::forget('trackingTotal');
                    $this->trackingCentre = $targetService->getExportTarget($params, true);
                    \Session::push('trackingTotal', $this->trackingCentre);
                }
                $ranking= $targetService->getRanking($this->trackingCentre, $params['centre'], $beginMonth, $currentYear, $params['acumulative']);
                
                return DataTables::of(collect($ranking))
                    ->addIndexColumn()
                    ->make(true);
            }
            return view('home', ['title' => $this->title ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados, contacte con el administrador');
        } catch (\Exception $e) {
            return json_encode([
                "draw" => 24,
                "recordsTotal" => count($ranking),
                "recordsFiltered" => 0,
                "data" => [],
                'length' => count($ranking),
                "error" => $e->getMessage()
            ]);            
        } 
    }

    public function getTargets(Request $request) {

        try {
            $params = $request->all();
            if (empty($params['monthYear'])) {
                $params['monthYear'] = date('m').'/'.date('Y'); 
            }    
            $month = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
            $year  = substr($params['monthYear'], strpos($params['monthYear'], '/') +1);
            $centreId = isset($params['centre_id']) ? $params['centre_id'] : null; 
            $vp = ['value' => 0, 'target' => 0];
            $vc = ['value' => 0, 'target' => 0];

            $targetService = new TargetService();
            $centres = null;
            if (!empty($centreId)) {
                $centres = Centre::where('id', $centreId)->get();
            } else {
                $centres = Centre::getCentersWithoutHCT();
            }
            $params['centre'] =  $centres;

            $targetDefined = $targetService->getTarget($centres, $month, $year); 

            if (empty($targetDefined)) {
                $cNames = [];
                foreach ($centres->toArray() as $centre) {
                    $cNames[] = is_array($centre) ? $centre['name']: $centre->name;
                } 
                $centresName = implode(',', $cNames); 
                throw new \Exception("Error no se ha definido objetivo para el centro: ".$centresName);
            }

            foreach ($targetDefined as $td) {
                $vp['value']  += $td->vd;
                $vp['target'] += $td->obj2;
            }

            \Session::forget('trackingCentre');

            $this->trackingCentre = \Session::get('trackingCentre');
            if (empty($this->trackingCentre)) {
                $this->trackingCentre = $targetService->getExportTarget($params);
                \Session::forget('trackingCentre');
                \Session::push('trackingCentre', $this->trackingCentre); 
            } else {
                $this->trackingCentre = \Session::get('trackingCentre')[0];
            }
            
            $tracking = []; 
            foreach ($this->trackingCentre->toArray() as $trackingCentreRow) {
                if (!isset($tracking[$trackingCentreRow->centre_employee])) {
                    $tracking[$trackingCentreRow->centre_employee] = []; 
                }
                $tracking[$trackingCentreRow->centre_employee] = []; 
            }
            foreach ($this->trackingCentre as $trackingCentreRow) {
                $tracking[$trackingCentreRow->centre_employee][] = $trackingCentreRow; 
            }

            $vcTotal =  $targetService->getVC($centres, $tracking); 
            $vc['value']  += $vcTotal;
            foreach ($targetDefined as $td) {
                $vc['target'] += $td->obj1; 
            }
            return json_encode(['data' =>  ['vp'  => $vp, 'vc'  => $vc]]); 

        } catch (\Exception $e) {
            //FIXME.... response error text alert doesn't works 
            return response()->json(
                [
                'success' => 'false',
                'errors'  => $e->getMessage(),
                ], 400
            ); 
            //Redirect::to('/calculateIncentive')->with('error', $e->getMessage());
        }   
    }

}
