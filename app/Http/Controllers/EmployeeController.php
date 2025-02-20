<?php

namespace App\Http\Controllers;

use Adldap\Laravel\Facades\Adldap;
use App\JobCategory;
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
use Illuminate\Auth\Events\Validated;


class EmployeeController extends DefaultLoginController
{
    public function __construct()
    {
        $this->title = 'Empleados';
        $this->user = session()->get('user');
        $this->copycauEmail = explode(',', env('MAIL_CC_CAU'));
    }

    public function index(Request $request)
    {
        try {
            //!ACCESO SOLO PARA ADMINISTRADORES
            $this->user = session()->get('user');
            if (!in_array($this->user->rol_id, [1, 3])) {
                return redirect(RouteServiceProvider::HOME)->with('error', 'Zona restringida');
            }
    
            if ($request->ajax()) {
                $employees = Employee::select(
                    'employees.id',
                    'employees.dni',
                    'employees.username',
                    'employees.name',
                    'employees.cancellation_date',
                    'employees.category',
                    'employees.count_access',
                    'employees.pending_password',
                    'employees.updated_at',
                    'roles.name as role',
                    'centres.name as centre'
                )
                    ->where(function ($query) {
                        $query->where('employees.cancellation_date', '>', date('Y-m-d'))
                            ->orWhereNull('employees.cancellation_date');
                    })
                    ->join('roles', 'roles.id', '=', 'rol_id')
                    ->leftJoin('centres', 'centres.id', '=', 'centre_id');
    
                // Filtrar por centro si el usuario tiene rol 3
                if ($this->user->rol_id == 3) {
                    $employees->where('employees.centre_id', $this->user->centre_id);
                }
    
                $employees = $employees
                    ->orderBy('employees.updated_at', 'desc')
                    ->orderByRaw('CASE WHEN employees.count_access = 3 THEN 0 ELSE 1 END')
                    ->orderBy('employees.name', 'asc');
    
                return Datatables::of($employees)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhere('employees.username', 'LIKE', "%$search%")
                                    ->orWhere('employees.name', 'LIKE', "%$search%")
                                    ->orWhere('centres.name', 'LIKE', "%$search%")
                                    ->orWhere('employees.dni', 'LIKE', "%$search%")
                                    ->orWhere('employees.category', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addColumn('action', function ($employee) {
                        // Si el usuario tiene rol 3, no mostrar botones de acción
                        if (session()->get('user')->rol_id == 3) {
                            return '';
                        }
    
                        // Botones de acción para otros roles
                        $btn = '<a href="employees/edit/' . $employee->id . '" class="btn-edit" data-editar="Editar">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>';
                        $btn .= '<a id="btnResetAccess' . $employee->id . '" onclick="resetAccessApp(' . $employee->id . ')" class="btn-reset-access" data-access="Resetear número de acceso">
                                    <span class="material-symbols-outlined">refresh</span>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                </a>';
                        $btn .= '<a id="btnResetPass' . $employee->id . '" onclick="resetPassword(' . $employee->id . ')" class="btn-validate-password" data-validate="Validación y nueva contraseña">
                                    <span class="material-symbols-outlined">person</span>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                </a>';
                        $btn .= '<a id="btnDenyAccess' . $employee->id . '" onclick="denyAccess(' . $employee->id . ')" class="btn-denegate-access" data-denegate="Denegar acceso">
                                    <span class="material-symbols-outlined">block</span>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                </a>';
                        $btn .= '<a id="btnSyncA3_' . $employee->id . '" onclick="syncA3(' . $employee->id . ', \'only\')" class="btn-sincro-a3" data-sincro="Sincronizar A3">
                                    <span class="material-symbols-outlined">sync</span>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                </a>';
    
                        return $btn;
                    })
                    ->rawColumns(['action', 'options'])
                    ->make(true);
            }
    
            return view('admin.employees.index', ['title' => $this->title,'user' => session()->get('user')]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados, contacte con el administrador');
        }
    }
    

    //! PENDING OF VALIDATION
    public function indexPending(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Se recogen sólo los usuarios que no están validados
                $pending_validate_employee = Employee::select(
                    'employees.username',
                    'employees.name',
                    'employees.dni',
                    'employees.email',
                    'a3_empleados.Nombre_Completo'
                )
                    ->join('a3_empleados', 'a3_empleados.NIF', '=', 'employees.dni')
                    ->distinct()
                    ->where('cancellation_date', '=', null)
                    ->where('employees.validated', '=', 0);

                return Datatables::of($pending_validate_employee)
                    ->addIndexColumn()
                    ->addColumn('action', function ($pending) {
                        $btn = '';
                        $btn = '<a data-toggle="modal" data-email="' . $pending->email . '" data-username="' . $pending->username . '" data-a3_nombre="' . $pending->Nombre_Completo . '"  data-pdi_nombre="' . $pending->name . '" title="Validar empleado" class="btn btn-success btnConfirmValidate" href="#modal-validate"> <span class="material-symbols-outlined mr-1">check</span>Validar</a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.employees.validated_employee', ['title' => 'Pendientes de validar']);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados, contacte con el administrador');
        }
    }

    //! EMPPLOYEE HISTORY
    public function history($id)
    {

        try {

            $employee_history = EmployeeHistory::select('employees.id', 'employees.name as employee', 'centres.name as centre', 'employee_history.created_at as fecha', 'roles.name as role')
                ->join('employees', 'employees.id', '=', 'employee_history.employee_id')
                ->join('centres', 'centres.id', '=', 'employee_history.centre_id')
                ->join('roles', 'roles.id', '=', 'employee_history.rol_id')
                ->where('employee_history.employee_id', '=', $id)
                ->orderBy('employee_history.created_at', 'desc')
                ->get();
            return Datatables::of($employee_history)
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar historico de empleados, contacte con el administrador');
        }
    }

    //! EDIT USER
    public function edit($id)
    {
        try {
            $employee = Employee::find($id);
            $roles = Role::getRolesActive();
            $centres = Centre::getCentresActive();
            $categories = JobCategory::all();
            $excludeMotives = (explode(',', env('EXCLUDE_RANKINGS')));

            $dayNow = date('Y-m-d');
            $dayYesterday = date('Y-m-d', strtotime('-1 days'));
            return view('admin.employees.edit', [
                'title' => $this->title,
                'employee' => $employee,
                'roles' => $roles,
                'centres' => $centres,
                'dayNow' => $dayNow,
                'dayYesterday' => $dayYesterday,
                'motives' => $excludeMotives,
                'categories' => $categories

            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados para editar, contacte con el administrador');
        }
    }

    //! UPDATE USER
    public function update(Request $request, $id)
    {
        try {

            app('App\Http\Controllers\HomeController')->editProfile($request, $id);

            return redirect()->action('EmployeeController@index')

                ->with('success', 'Empleado actualizado correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al editar empleados, contacte con el administrador');
        }
    }

    public function validateEmployee(Request $request, $username)
    {
        try {
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al intentar validar al empleado ' . $username . ', contacte con el administrador');
        }
    }

    //! CONFIRM USERNAME

    public function confirmUsername(Request $request)
    {
        try {
            if ($request->ajax()) {
                $params = $request->all();
                $employee = Employee::where('username', $params['username']);
                $employeeData = $employee->first();
                if (!empty($employeeData->toArray() && empty($employeeData->email))) {
                    return response()->json([
                        'success' => 'false',
                        'errors' => 'Error usuario no encontrado'
                    ], 400);
                }

                $specialChars = "!@#$%^&*()_-+=`~,<>.[]: |{}\\;\"'?/";
                $specialChar = substr(str_shuffle(str_repeat($specialChars, 5)), 0, 1);
                $randomPass = Str::random(7) . $specialChar;
                $pass = Hash::make($randomPass);
                $fields['validated'] = 1;
                $fields['password'] = $pass;
                $employee->update($fields);

                $emailData = [
                    'username' => $params['username'],
                    'password' => $randomPass,
                    'subject' => 'Acceso para la app ICOT PDI',
                    'view' => 'emails.first_access_data_registered_employee'
                ];
                Mail::to($employeeData->email)->send(new RegisteredUser($emailData));

                return json_encode(['data' => ['username' => $params['username']]]);
            }
            return view('admin.employees.validated_employee', ['title' => $this->title]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => 'false',
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    //! CHANGE USERNAME

    public function changeUsername(Request $request)
    {
        try {
            if ($request->ajax()) {
                $params = $request->all();
                $employee = Employee::where('username', $params['username']);

                $fields['username'] = $params['username'];
                $fields['name'] = $params['name'];
                $fields['email'] = $params['email'];

                $employee->update($fields);
                return json_encode(['data' => ['username' => $params['username']]]);
            }
            return view('admin.employees.validated_employee', ['title' => $this->title]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => 'false',
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    //! RESERT COUNT ACCESS
    public function resetAccessApp(Request $request)
    {
        try {
            $params = $request->all();
            $idEmployee = (int) $params['employee_id'];
            $employee = Employee::where('id', $idEmployee)->first(); // Asegurarse de que el empleado existe

            if (!$employee) { // Verificar si el empleado no fue encontrado
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Empleado no encontrado'
                ], 404);
            }

            // Se procede con el reseteo de acceso independientemente del estado del correo electrónico
            $resultado = Employee::updatingAccess($idEmployee, 0);

            if (!$resultado) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Error al Desbloquear Cuenta'
                ], 500);
            }

            if (!empty($employee->email)) {
                $emailData = [
                    'subject' => 'Reseteo de Acceso',
                    'view' => 'emails.template_unlockAccount',
                ];

                Mail::to($employee->email)
                    ->cc($this->copycauEmail)
                    ->send(new RegisteredUser($emailData));

                \Log::debug('CC Emails:', $this->copycauEmail);
            } else {
                \Log::info('Reseteo de acceso realizado, empleado sin correo electrónico registrado.', ['employee_id' => $idEmployee]);
            }

            // Actualizar la fecha de la última modificación del empleado
            $employee->updated_at = now();
            $employee->save();

            $hasEmail = !empty($employee->email);
            $success = $hasEmail ? true : false;
            $mensaje = $hasEmail ? 'Cuenta desbloqueada' : 'Cuenta desbloqueada. Sin Correo Asociado';

            return response()->json([
                'success' => $success,
                'mensaje' => $mensaje
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error en la base de datos'
            ], 500);
        }
    }

    //! VALIDATE USER AND NEW PASSWORD

    public function resetPassword(Request $request)
    {
        try {
            $excludeCategories = ['SUPERVISOR CENTRO DE REHABILITACIÓN', 'Aux. Administrativo/a TFE', 'Auxiliares Administrativos', 'AUXILIAR ADMINISTRATIVO/A', 'JEFE/A ADMINISTRACION', 'AUX. ADMINISTRATIVO/A'];
            $excludeCategories = array_map('strtoupper', $excludeCategories);
            $employee = Employee::findOrFail($request->employee_id);
            $hasEmail = !empty($employee->email);
            $success = $hasEmail ? true : false;
            $mensaje = $hasEmail ? 'Usuario Validado Correctamente' : 'Sin Correo Asociado';

            if (in_array(strtoupper($employee->category), $excludeCategories)) {
                $employee->validated = 1;
                $employee->save();

                if (!empty($employee->email)) {
                    $emailData = [
                        'subject' => 'Validación de cuenta',
                        'view' => 'emails.template_validateExcludeCategory',
                    ];

                    Mail::to($employee->email)
                        ->cc($this->copycauEmail)
                        ->send(new RegisteredUser($emailData));

                    \Log::debug('CC Emails:', $this->copycauEmail);

                    $employee->updated_at = now();
                    $employee->save();

                }
               

                return response()->json([
                    'success' => $success,
                    'mensaje' => $mensaje
                ], 200);


            } else {
                $employee->password = 'abc.1234';
                $hashedPassword = Hash::make($employee->password);
                $employee->password = $hashedPassword;
                $employee->validated = 1;
                $employee->count_access = 0;
                $employee->pending_password = 0;
                $employee->save();

                if (!empty($employee->email)) {
                    $emailData = [
                        'subject' => 'Asignación de nueva contraseña',
                        'view' => 'emails.template_newPassword',
                        'username' => $employee->username,
                    ];

                    Mail::to($employee->email)
                        ->cc($this->copycauEmail)
                        ->send(new RegisteredUser($emailData));

                    \Log::debug('CC Emails:', $this->copycauEmail);

                    $employee->updated_at = now();
                    $employee->save();
                }

                return response()->json([
                    'success' => $success,
                    'mensaje' => $mensaje
                ], 200);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'mensaje' => 'Empleado No Encontrado'], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['success' => false, 'mensaje' => 'Error de Base de Datos'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'mensaje' => 'Error general: ' . $e->getMessage()], 500);
        }
    }

    //! DENY USER ACCESS

    public function denyAccessApp(Request $request)
    {
        try {
            $params = $request->all();
            $employee = Employee::where('id', $params['employee_id'])->first();
            Employee::updatingAccess($employee['id'], 3);
            $tokenRepository = app('Laravel\Passport\TokenRepository');
            $tokenId = DB::table('oauth_access_tokens')->where('user_id', $employee->id)->first();
            if (!empty($tokenId)) {
                $tokenId = $tokenId->id;
                $tokenRepository->revokeAccessToken($tokenId);
                return response()->json([
                    'success' => true,
                    'url' => '/admin/employees',
                    'mensaje' => 'Cuenta Bloqueada.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'url' => '/admin/employees',
                    'mensaje' => 'No Encontrado Access Token.'
                ], 404);
            }
        } catch (\Illuminate\Database\QueryException $e) {

            return response()->json([
                'success' => true,
                'url' => null,
                'mensaje' => 'Error de Base de Datos'
            ], 200);
        }
    }

    //! SYNC USER A3

    public function syncA3(Request $request)
    {
        $params = $request->all();
        $idEmployee = (int) $params['employee_id'];
        $this->user = session()->get('user');

        \Log::channel('a3')->info("Iniciado forzado de Sync A3 desde PDI-Web, realizado por usuario: " . $this->user->username);
        if ($params['type'] == 'full') {
            try {
                Artisan::call('a3empleados:cron', []);
            } catch (\Exception $e) {
                \Log::channel('a3')->info('Error' . $e->getMessage());
            }
        } else {
            $employee = Employee::find($idEmployee);

            if (!empty($employee->dni)) {
                if (!empty($employee->name)) {
                    Log::channel('a3')->info("Sync A3 desde PDI-Web, forzando usuario con DNI: " . $employee->dni);
                    try {
                        Artisan::call('a3empleados:cron', ['dni' => $employee->dni, 'name' => $employee->name]);
                    } catch (\Exception $e) {
                        \Log::channel('a3')->info('Error' . $e->getMessage());
                    }
                } else {
                    Log::channel('a3')->info("Sync A3 desde PDI-Web, forzando usuario con DNI: " . $employee->dni);
                    try {
                        Artisan::call('a3empleados:cron', ['dni' => $employee->dni]);
                    } catch (\Exception $e) {
                        \Log::channel('a3')->info('Error' . $e->getMessage());
                    }
                }
            } else {
                Log::channel('a3')->info("Sync A3 desde PDI-Web, forzando usuario con Nombre: " . $employee->name);
                try {
                    Artisan::call('a3empleados:cron', ['name' => $employee->name]);
                } catch (\Exception $e) {
                    \Log::channel('a3')->info('Error' . $e->getMessage());
                }
            }
        }
        // añade el campo de categorías para PDI

        $this->syncJobCategories();

        Log::channel('a3')->info("Finalizado forzado de Sync A3 desde PDI-Web, realizado por usuario: " . $this->user->username);
        session()->flash('success', 'Sincronización Correcta');
        return view('admin.employees.index', ['title' => $this->title])->with('success', 'Sincronización Realizada Correctamente.');
    }


   
    public function syncJobCategories()
{
    // Obtener todas las categorías con sus job_category_id correspondientes
    $categoryJobCategories = DB::table('category_job_category')->get()->mapWithKeys(function ($item) {
        return [strtolower(trim(preg_replace('/\s+/', ' ', $item->category_name))) => $item->job_category_id];
    });

    // Recorrer todos los empleados en bloques para evitar problemas de memoria
    Employee::chunk(100, function ($employees) use ($categoryJobCategories) {
        foreach ($employees as $employee) {
            if (!empty($employee->category)) {
                $normalizedCategory = strtolower(trim(preg_replace('/\s+/', ' ', $employee->category)));
                // Buscar el job_category_id correspondiente en category_job_category
                if (isset($categoryJobCategories[$normalizedCategory])) {
                    $employee->update(['job_category_id' => $categoryJobCategories[$normalizedCategory]]);
                    Log::info("Updated employee ID {$employee->id} with job_category_id {$categoryJobCategories[$normalizedCategory]} for category {$employee->category}");
                } else {
                    Log::warning("No matching category found for employee ID {$employee->id} with category '{$employee->category}' (normalized to '{$normalizedCategory}')");
                }
            } else {
                Log::warning("Employee ID {$employee->id} has an empty category field.");
            }
        }
    });

    return redirect()->back()->with('success', 'Categorías de empleados sincronizadas correctamente.');
}

}