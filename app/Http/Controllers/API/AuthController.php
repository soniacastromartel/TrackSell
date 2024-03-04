<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Employee;
use Adldap\Laravel\Facades\Adldap;
use App\A3Empleado;
use App\Mail\RegisteredUser;
use Illuminate\Support\Facades\Mail;
use Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use Exception;
use App\A3Employee;

//use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    private $cauEmail;

    public function __construct()
    {   
        $this->cauEmail      =  env('MAIL_TO_CAU');
        $this->copycauEmail  =  explode(',', env('MAIL_CC_CAU'));
    }

    /**
     * REGISTRO DE NUEVO EMPLEADO
     */
    public function register(Request $request)
    {
        \Log::channel('api')->info("Inicio Solicitud Acceso!");
        $params = $request->all();
        \Log::channel('api')->info("Datos recibidos");
        \Log::channel('api')->info($params);

        // Validación que viene datos mínimos desde Front
        $validator = Validator::make($request->all(), [
            'dni' => 'required',
            'name' => 'required',
            'infoDevice' => 'required'
        ]);
        if ($validator->fails()) {
            \Log::channel('api')->info("Error solicitud acceso, error de validación  " . $validator->errors());
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $userLdap = [];
        $userExists = Employee::where(['dni' => $params['dni']]);
        $user = $userExists->first();
        if (!empty($user)) {
            $userLdap = $user->toArray();
        }

        if (!empty($userLdap)) {
            // EL USUARIO YA SE HA VALIDADO
            if ($userLdap['validated'] != -1) {
                \Log::channel('api')->info("Error solicitud acceso, usuario ya validado");
                return $this->sendError('User validated no es -1.');
            }

            //Comprobación que usuario existe en A3
            $pending_validate_employee = A3Employee::where(['identifierNumber' => $params['dni']]);
            $userA3 = $pending_validate_employee->first();
            if (empty($userA3)) {
                $errormessage = "Error solicitud acceso, empleado no se encuentra en A3, se envía correo a " . $this->cauEmail;
                foreach ($this->copycauEmail as $cc) {
                    $errormessage = ';' . $cc;
                }
                \Log::channel('api')->info($errormessage);
                $this->sendEmailErrorRegister($params);
            } else {
                // Se define una contraseña temporal para usarios nuevos, 
                // hasta que se proceda a su validación.
                $userData['pending_password'] = env('CHANGING_PASS_WORD');
                $userData['password'] = env('RESPONSE_PENDING_VALIDATION');
                $userData['validated'] = 0;
                $userData['infoDevice'] = $params['infoDevice'];
                $userData['password'] = bcrypt($request->password);

                $userExists->update($userData);
                $message = "Ok procesada solicitud acceso, se envía correo a " . $this->cauEmail;
                foreach ($this->copycauEmail as $cc) {
                    $message = ';' . $cc;
                }
                \Log::channel('api')->info($message);
                $this->sendEmailOkRegister($params);
                return $this->sendResponse(
                    [
                        'username'      => $userLdap['username'], 'name'        => $userLdap['name'], 'dni'         => $userLdap['dni']
                    ],
                    'ok'
                );
            }
        } else {
            $errormessage = "Error solicitud acceso, empleado no se encuentra en AD, se envía correo a " . $this->cauEmail;
            foreach ($this->copycauEmail as $cc) {
                $errormessage = ';' . $cc;
            }
            \Log::channel('api')->info($errormessage);
            $this->sendEmailErrorRegister($params);
        }
        \Log::channel('api')->info("Fin Solicitud Acceso!");
    }
    /**
     * Solicitud Desbloqueo
     */
    public function unlockRequest(Request $request)
    {
        \Log::channel('api')->info("Solicitud de Desbloqueo");
        $params = $request->all();
        \Log::channel('api')->info("Datos recibidos");
        \Log::channel('api')->info($params);

        // Validación que viene datos mínimos desde Front
        $validator = Validator::make($request->all(), [
            'username' => 'required',
        ]);
        if ($validator->fails()) {
            \Log::channel('api')->info("Error solicitud desbloqueo, error de validación  " . $validator->errors());
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $this->sendEmailUnlockRequest($params);
        $this->unlockRequestUpdate($params);
    }

    /** Metodo procesa correo de error en solicitud acceso ( no se encuentra en AD / A3 ) */

    public function sendEmailErrorRegister($params)
    {

        $user = [];
        // EL USUARIO NO EXISTE EN LA BD
        $user['dni']        = $params['dni'];
        $user['name']       = $params['name'];
        $user['infoDevice'] = $params['infoDevice'];

        $emailData = $user;
        $emailData['subject'] = 'Error en solicitud accesso de empleado, no se encuentran datos ';
        $emailData['view']    = 'emails.registered_employee';

        /**
         * ENVIAR CORREO ERROR SOLICITUD ACCESO
         */
        Mail::to([$this->cauEmail])
            ->cc($this->copycauEmail)
            ->send(new RegisteredUser($emailData));
        return $this->sendResponse([], env('ERROR_A3_VALIDATION'));
    }

    /** Metodo procesa correo de  solicitud acceso */
    public function sendEmailOkRegister($params)
    {
        $user = [];
        // EL USUARIO NO EXISTE EN LA BD
        $user['dni']        = $params['dni'];
        $user['name']       = $params['name'];
        $user['infoDevice'] = $params['infoDevice'];

        $emailData = $user;
        $emailData['subject'] = 'Se ha recibido una nueva solicitud accesso de empleado';
        $emailData['view']    = 'emails.registered_employee';


        /**
         * ENVIAR CORREO  SOLICITUD ACCESO
         */
        Mail::to([$this->cauEmail])
            ->cc($this->copycauEmail)
            ->send(new RegisteredUser($emailData));
        return $this->sendResponse([], env('REQUESTED'));
    }


    /** Metodo procesa correo de  solicitud desbloqueo */
    public function sendEmailUnlockRequest($params)
    {
        try {
            \Log::channel('app')->info("Inicio Envio Correo");
            $user = [];
            $user['username']       = $params['username'];
            $user['name']       = $params['name'];

            $emailData = $user;
            $emailData['subject'] = 'Se ha recibido una solicitud de desbloqueo de cuenta';
            $emailData['view']    = 'emails.blocked_account';


            /**
             * ENVIAR CORREO  SOLICITUD DESBLOQUEO
             */
            Mail::to([$this->cauEmail])
                ->cc($this->copycauEmail)
                ->send(new RegisteredUser($emailData));
            return $this->sendResponse([], env('REQUESTED'));
        } catch (Exception $e) {
            \Log::channel('appError')->info($e->getMessage());
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }
    
    //! ACTUALIZA EL UNLOCKREQUEST EN LA BASE DE DATOS (SOLICITUD DE DESBLOQUEO PREVIA: FALSE, TRUE)
  
    public function unlockRequestUpdate($params)
    {
        \Log::channel('app')->info("Inicio update UnlockRequest");

        $user       = $params['username'];
        if (!empty($user)) {
            $employee = Employee::whereRaw("BINARY username = ?", [$user])->first();
            if (!empty($employee)) {
                try {
                    $employee->update(['unlockRequest' => 1]);
                    return $this->sendResponse('', 200);
                } catch (Exception $e) {
                    \Log::channel('appErrpr')->info($e->getMessage());
                    return response()->json(
                        [
                            'success' => 'false',
                            'errors'  => $e->getMessage(),
                        ],
                        400
                    );
                }
            }
        }
    }
   
     //!LOGIN

    public function login(Request $request)
    {
        $initResult = $this->statusEmployee($request);
        if ($initResult == env('RESPONSE_OK')) {
            return $this->accessToEmployee($request);
        } else {
            return $this->sendError($initResult);
        }
    }

    //! METODO AUXILIAR, REALIZA LA COMPROBACIÓN DEL ESTADO ACTUAL DEL EMPLEADO 

    protected function statusEmployee(Request $request)
    {
        $params = $request->all();
        $employee = Employee::whereRaw("BINARY username = ?", [$params['username']])
            ->where(function ($query) {
                $query->where('cancellation_date', '>', date('Y-m-d'))
                    ->orWhereNull('cancellation_date');
            })
            ->get();
        $employee = $employee->toArray();
        if (!empty($employee)) {
            // Usuario existe
            $param = $request->all();
            $employee = Employee::where('username', $param['username'])->get();
            if ($employee[0]['validated'] == 1) {
                // Usuario existe y está validado
                if ($employee[0]['pending_password'] == null) {
                    if ($employee[0]['count_access'] >= 3) {
                        return env('ACCOUNT_BLOCK');
                    } else {
                        // Usuario existe, está validado y tiene su contraseña definida
                        Employee::updatingAccess($employee[0]['employee_idd'], 0);
                        return env('RESPONSE_OK');
                    }
                } else if ($employee[0]['pending_password'] == env('RESPONSE_PENDING_VALIDATION')) {
                    // Pendiente de validación
                    return env('RESPONSE_PENDING_VALIDATION');
                } else {
                    // Pendiente de regeneración de nueva contraseña
                    return env('RESPONSE_PENDING_CHANGE_PASS');
                }
            } else {
                // Usuario existe pero no está validado
                return env('RESPONSE_PENDING_VALIDATION');
            }
        } else {
            // Usuario no existe en el sistema.
            return env('RESPONSE_NO_VALID');
        }
    }

    //! METODO AUXILIAR ACCESO LOGIN 

    public function accessToEmployee(Request $request)
    {
        $loginData = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $ldapAccess = false;
        if (!Auth::guard('web_local')->attempt($request->only('username', 'password'))) {
            $ldapAccess = true;
            if (!Auth::attempt($request->only('username', 'password'))) {
                return $this->sendError('Invalid Credentials');
            }
        }

        $employeeData = [];
        if ($ldapAccess) {
            $success['access_token'] = auth()->user()->createToken('authToken')->accessToken;
            $employeeData = auth()->user();
        } else {
            $success['access_token'] = Auth::guard('web_local')->user()->createToken('authToken')->accessToken;
            $employeeData = Auth::guard('web_local')->user();
        }
        $auxEmployeeData = $employeeData->toArray();
        $eData = new Employee;
        $eData->id               = $auxEmployeeData['id'];
        $eData->name             = $auxEmployeeData['name'];
        $eData->username         = $auxEmployeeData['username'];
        $eData->centre_id        = $auxEmployeeData['centre_id'];
        $eData->validated        = $auxEmployeeData['validated'];
        $eData->pending_password = $auxEmployeeData['pending_password'];
        $eData->email            = $auxEmployeeData['email'];
        $eData->dni              = $auxEmployeeData['dni'];
        $eData->phone            = $auxEmployeeData['phone'];
        $eData->category         = $auxEmployeeData['category'];
        $eData->count_access     = $auxEmployeeData['count_access'];
        $eData->unlockRequest    = $auxEmployeeData['unlockRequest'];
        // $eData->img              = $auxEmployeeData['img'];
        $success['user']         = $eData;

        // Reseteo de contador de acceso tras inicio correcto
        $employee = Employee::where('username', $request['username']);
        $employeeArray = $employee->get()->toArray();

        // Se guarda la version actual del dispositivo
        Employee::updatingRegistryVersion($employeeArray[0]['username'], $request['version']);
        // Control conteo de accesos
        if (!empty($employee)) {
            Employee::updatingAccess($success['user']['id'], 0);
            Employee::updatingUnlockRequest($request['username']); //TODO metodo para update unlockRequest añadir
        }
        return $this->sendResponse($success, 'Usuario logueado!!');
    }
    
    //!SOLICITUD DE RECUPERACIÓN DE CONTRASEÑA DE EMPLEADOS

    public function recoveryPass(Request $request)
    {
        $employee_exists = $this->statusEmployee($request);
        $params = $request->all();
        $sct = $params['secret'];
        if ($employee_exists == env('RESPONSE_OK')) {
            if ($sct != null) {
                if ($sct == 'InformaticTeam') {
                    if ($params['name'] != null) {
                        $isValid = $this->getName($request);
                        $userName = $params['username'];
                        if ($userName != null) {
                            if ($isValid) {
                                return $this->sendResponse([], env('RESPONSE_OK'));
                            } else {
                                return $this->sendResponse([], env('HACK'));
                            }
                        } else {
                            return $this->sendResponse([], env('REQUESTED'));
                        }
                    } else {
                        return $this->sendResponse([], env('REQUESTED'));
                    }
                } else {
                    // ACCESO DENEGADO
                    return $this->sendResponse([], env('INVALID_SECRET'));
                }
            } else {
                return $this->sendResponse([], env('REQUESTED'));
            }
        } else {
            switch ($employee_exists) {
                case 'pending_password':
                    if ($this->getName(($request))) {
                        return $this->sendResponse([], env('RESPONSE_OK'));
                    } else {
                        return $this->sendResponse([], env('REQUESTED'));
                    }
                    break;
                case 'pending_validation':
                    return $this->sendResponse([], env('RESPONSE_PENDING_VALIDATION'));
                case 'no_valid':
                    return $this->sendResponse([], env('RESPONSE_NO_VALID'));
                case 'block':
                    return $this->sendResponse([], env('ACCOUNT_BLOCK'));
            }
        }
    }

    /**
     * Comprueba que el nombre recibido es el mismo que el del 
     * empleado guardado en el sistema
     */
    public function getName(Request $request)
    {
        $params = $request->all();
        $username = $params['username'];
        $exists = Employee::where(['username' => $username, 'name' => $params['name']])->first();
        return !empty($exists);
    }


    /**
     * Cambio automático de password por parte del usuario
     */
    public function changingPass(Request $request)
    {
        $params = $request->all();


        $user = Employee::where(function ($query) use ($params) {
            if (isset($params['dni'])) {
                $query->where('dni', '=', $params['dni']);
            }
            if (isset($params['name'])) {
                $query->where('name', '=', $params['name']);
            }
            if (isset($params['username'])) {
                $query->where('username', '=', $params['username']);
            }
        })->first();

        if (!empty([$user])) {
            $pass = Hash::make($params['password']);
            $user->update(['password' => $pass, 'pending_password' => null]);
            return $this->sendResponse([], env('RESPONSE_OK'));
        } else {
            return $this->sendResponse([], env('IDENTIFICATION_ERROR'));
        }
    }
}
