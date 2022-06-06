<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


use App\Http\Controllers\Auth\LoginController as DefaultLoginController;
use DataTables;
use App\Employee;
use App\Role; 
use App\Centre;
use App\EmployeeHistory;
use DB;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Mail\RegisteredUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Artisan;

class EmployeeController extends DefaultLoginController //Controller 
{

    public function __construct()
    {
        $this->title = 'Empleados';
        $this->user = session()->get('user');
    }


    public function index(Request $request) {
        try{
             //ACCESO SOLO PARA ADMINISTRADORES
             $this->user = session()->get('user');
            if (!in_array($this->user->rol_id, [1,3])) {
                return redirect(RouteServiceProvider::HOME)->with('error','Zona restringida'); 
            }

            if ($request->ajax()) {
                $employees = Employee::select('employees.id'
                                              ,'employees.dni'  
                                              ,'employees.username'
                                              ,'employees.name'
                                              ,'employees.cancellation_date'
                                              ,'roles.name as role'
                                              ,'centres.name as centre')
                    ->where(function($query) {
                        $query->where('employees.cancellation_date','>',date('Y-m-d'))
                                ->orWhereNull('employees.cancellation_date');
                    })
                    ->join('roles','roles.id','=','rol_id')
                    ->leftJoin('centres','centres.id','=','centre_id');
                    //->orderBy('name');
                    //->get();
                return  Datatables::of($employees)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        
                        if (!empty($request->get('search'))) {
                             $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('employees.username', 'LIKE', "%$search%")
                                ->orWhere('employees.name', 'LIKE', "%$search%")
                                ->orWhere('centres.name', 'LIKE', "%$search%")
                                ->orWhere('employees.dni', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addColumn('action', function($employee){
                        $btn = '';
                        $btn = '<a href="employees/edit/'.$employee->id.'" class="btn btn-warning a-btn-slide-text"><bold>Editar</bold></a>';
                        $fnCall = 'resetAccessApp('.$employee->id.' )'; 
                        $btn .= '<a onclick="'. $fnCall .'"  class="btn btn-success a-btn-slide-text"><bold>Reestablecer Acceso</bold></a>';
                        $fnCall = 'denyAccess('.$employee->id.' )'; 
                        $btn .= '<a onclick="'. $fnCall .'" class="btn btn-red-icot a-btn-slide-text"><bold>Denegar Acceso</bold></a>';
                        $fnCall = 'syncA3('.$employee->id.' , \'only\')'; 
                        $btn .= '<a id="btnSyncA3_'.  $employee->id. '" onclick="'. $fnCall .'" class="btn a-btn-slide-text" style="background: #00838f;"><bold>Sincronizar A3</bold></a>';
                        $btn.= '<button id="btnSubmitLoad_'  .  $employee->id. '" type="submit" class="btn btn-success" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Realizando sincronización...
                               </button>';
                        return $btn;
                    })
                    ->rawColumns(['action','options'])
                    ->make(true);
            }
            return view('admin.employees.index', ['title' => $this->title ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados, contacte con el administrador');
        }
    }

    public function indexPending(Request $request) { 
        try{
            if ($request->ajax()) {
                // Se recogen sólo los usuarios que no están validados
                $pending_validate_employee = Employee::select(  'employees.username'
                                                              , 'employees.name'
                                                              , 'employees.dni'
                                                              , 'employees.email'
                                                              , 'a3_empleados.Nombre_Completo')
                                            ->join('a3_empleados', 'a3_empleados.NIF', '=' , 'employees.dni')
                                            ->distinct()
                                            ->where('cancellation_date', '=', null) 
                                            ->where('employees.validated','=', 0);

                return Datatables::of($pending_validate_employee)
                ->addIndexColumn()
                ->addColumn('action', function($pending){
                    $btn = '';
                    $btn = '<a data-toggle="modal" data-email="'. $pending->email. '" data-username="'. $pending->username. '" data-a3_nombre="' . $pending->Nombre_Completo . '"  data-pdi_nombre="' .$pending->name. '" title="Validar empleado" class="btn btn-success btnConfirmValidate" href="#modal-validate">Validar</a>'; 
                    return $btn;
                })
                ->rawColumns(['action'])
                        ->make(true);
            }
            return view('admin.employees.validated_employee',['title' => 'Pendientes de validar']);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados, contacte con el administrador');
        }
    }

    public function history($id) {
        
        try{
            
            $employee_history = EmployeeHistory::select('employees.id','employees.name as employee', 'centres.name as centre', 'employee_history.created_at as fecha', 'roles.name as role')
                ->join('employees','employees.id','=','employee_history.employee_id')
                ->join('centres','centres.id','=','employee_history.centre_id')
                ->join('roles','roles.id','=','employee_history.rol_id')
                ->where('employee_history.employee_id' ,'=', $id)
                ->orderBy('employee_history.created_at', 'desc')
                ->get();
            return  Datatables::of($employee_history)
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar historico de empleados, contacte con el administrador');
        }
    }

    public function edit($id)
    {
        try{
            $employee = Employee::find($id);
            $roles = Role::getRolesActive();
            $centres = Centre::getCentresActive();
            $excludeMotives = (explode(',', env('EXCLUDE_RANKINGS')));

            $dayNow = date('Y-m-d'); 
            $dayYesterday = date('Y-m-d'  , strtotime( '-1 days' )); 
            return view('admin.employees.edit', ['title'     => $this->title
                                                , 'employee' => $employee
                                                , 'roles'    => $roles
                                                , 'centres'  => $centres
                                                , 'dayNow'   => $dayNow
                                                , 'dayYesterday' => $dayYesterday
                                                , 'motives' => $excludeMotives

            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados para editar, contacte con el administrador');
        }
    }
    
    
    public function update(Request $request, $id)
    {
        try{

            app('App\Http\Controllers\HomeController')->editProfile($request, $id);

            return redirect()->action('EmployeeController@index')

                        ->with('success','Empleado actualizado correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al editar empleados, contacte con el administrador');
        }
    }

    public function validateEmployee(Request $request, $username){
        try{
            
            
        } catch(\Illuminate\Database\QueryException $ex){
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al intentar validar al empleado '. $username .', contacte con el administrador');
        }
    }

    public function confirmUsername(Request $request) { 
        try{
            if ($request->ajax()) {
                $params = $request->all();
                $employee = Employee::where('username' , $params['username']);
                $employeeData = $employee->first(); 
                if (!empty($employeeData->toArray() && empty($employeeData->email) )) {
                    return response()->json([
                        'success' => 'false',
                        'errors'  => 'Error usuario no encontrado'
                    ],400);
                }
                
                $specialChars = "!@#$%^&*()_-+=`~,<>.[]: |{}\\;\"'?/";
                $specialChar = substr(str_shuffle(str_repeat($specialChars, 5)), 0, 1);
                $randomPass = Str::random(7) . $specialChar;
                $pass = Hash::make($randomPass);
                $fields['validated'] = 1; 
                $fields['password'] = $pass;
                $employee->update($fields);

                $emailData = [    'username' => $params['username']
                                , 'password' => $randomPass
                                , 'subject'  => 'Acceso para la app ICOT PDI'
                                , 'view'     => 'emails.first_access_data_registered_employee']; 
                Mail::to($employeeData->email)->send(new RegisteredUser($emailData));
                
                return json_encode(['data' =>  ['username'  => $params['username']]]); 
            }
            return view('admin.employees.validated_employee',['title' => $this->title ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ],400); 
            
        }
    }

    public function changeUsername(Request $request) { 
        try{
            if ($request->ajax()) {
                $params = $request->all(); 
                $employee = Employee::where('username' , $params['username']);

                $fields['username'] = $params['username'];  
                $fields['name']     = $params['name'];  
                $fields['email']    = $params['email'];  
                
                $employee->update($fields);
                return json_encode(['data' =>  ['username'  => $params['username']]]); 
                
            }
            return view('admin.employees.validated_employee',['title' => $this->title ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ],400); 
            
        }
    }


    public function denyAccessApp(Request $request){
        try{
            $params = $request->all(); 
            $employee = Employee::where('id' , $params['employee_id'])->first();
            Employee::updatingAccess($employee['id'], 3);
            $tokenRepository = app('Laravel\Passport\TokenRepository');
            $tokenId = DB::table('oauth_access_tokens')->where('user_id', $employee->id)->first();
            if (!empty($tokenId)) {
                $tokenId = $tokenId->id; 
                $tokenRepository->revokeAccessToken($tokenId);
                return response()->json(['success' => true
                                        , 'url'    => '/admin/employees'
                                        , 'mensaje'=> 'Denegado acceso'], 200);
            } else {
                return response()->json(['success' => false
                                        , 'url'    => '/admin/employees'
                                        , 'mensaje'=> 'No encontrado access token'], 200);
            }
            
            
        } catch (\Illuminate\Database\QueryException $e) {
            
            return response()->json(['success' => true
                                    , 'url'    => null
                                    , 'mensaje'=> 'Error'], 200);
            
        }    
    }

    /**
     * Reseteo contador de accesos desde el back-end
     */
    public function resetAccessApp(Request $request)
    {
        try{
            $params = $request->all();
            $idEmployee = (int)$params['employee_id'];
            $resultado = Employee::updatingAccess($idEmployee, 0);      
            
            if ($resultado) {
                return response()->json(['success' => true
                , 'url'    => null
                , 'mensaje'=> 'Se ha reseteado contador de accesos'], 200);
            } else {
                return response()->json(['success' => true
                , 'url'    => null
                , 'mensaje'=> 'Error'], 200);

            }
           
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['success' => true
                                    , 'url'    => null
                                    , 'mensaje'=> 'Error'], 200);
        }  
    }

    /**
     * Sincronizar usuarios con A3 (por dni o por nombre en su defecto)
     */
    public function syncA3(Request $request)
    {
        $params = $request->all();
        $idEmployee = (int)$params['employee_id'];
        $this->user = session()->get('user');

        Log::channel('a3')->info("Inciiado forzado de Sync A3 desde PDI-Web, realizado por usuario: " . $this->user->username);
        if ($params['type'] == 'full') {
            Artisan::call('a3empleados:cron',[]);
        } else {
            $employee = Employee::find($idEmployee); 

            if (!empty($employee->dni)){
                Log::channel('a3')->info("Sync A3 desde PDI-Web, forzando usuario con DNI: " . $employee->dni);
                Artisan::call('a3empleados:cron', ['dni' => $employee->dni]); 
            } else {
                Log::channel('a3')->info("Sync A3 desde PDI-Web, forzando usuario con Nombre: " . $employee->name);
                Artisan::call('a3empleados:cron', ['name' => $employee->name]); 
            }
        }
        Log::channel('a3')->info("Finalizado forzado de Sync A3 desde PDI-Web, realizado por usuario: " . $this->user->username);
        return view('admin.employees.index', ['title' => $this->title ])->with('sucess', 'Se ha sincronizado usuarios');
    }

}
