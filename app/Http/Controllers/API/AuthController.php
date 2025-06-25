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
use App\Services\EmployeeService;

//use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    private $cauEmail;

    public function __construct()
    {
        $this->cauEmail = env('MAIL_TO_CAU');
        $this->copycauEmail = explode(',', env('MAIL_CC_CAU'));
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
                        'username' => $userLdap['username'],
                        'name' => $userLdap['name'],
                        'dni' => $userLdap['dni']
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
        $user['dni'] = $params['dni'];
        $user['name'] = $params['name'];
        $user['infoDevice'] = $params['infoDevice'];

        $emailData = $user;
        $emailData['subject'] = 'Error en solicitud accesso de empleado, no se encuentran datos ';
        $emailData['view'] = 'emails.registered_employee';

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
        $user['dni'] = $params['dni'];
        $user['name'] = $params['name'];
        $user['infoDevice'] = $params['infoDevice'];

        $emailData = $user;
        $emailData['subject'] = 'Se ha recibido una nueva solicitud accesso de empleado';
        $emailData['view'] = 'emails.registered_employee';
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
            $user['username'] = $params['username'];
            $user['name'] = $params['name'];

            $emailData = $user;
            $emailData['subject'] = 'Se ha recibido una solicitud de desbloqueo de cuenta';
            $emailData['view'] = 'emails.blocked_account';


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
                    'errors' => $e->getMessage(),
                ],
                400
            );
        }
    }

    //! ACTUALIZA EL UNLOCKREQUEST EN LA BASE DE DATOS (SOLICITUD DE DESBLOQUEO PREVIA: FALSE, TRUE)
    public function unlockRequestUpdate($params)
    {
        $username = $params['username'] ?? null;

        if (!$username) {
            return $this->sendError('Nombre de usuario no proporcionado');
        }
        try {
            $updated = Employee::updatingUnlockRequest($username, env('LOCK'));
            if (!$updated) {
                return $this->sendError('Empleado no encontrado o inactivo', [], 404);
            }
            return $this->sendResponse(null, 'Unlock request actualizado correctamente');
        } catch (Exception $e) {
            return $this->sendError(
                'Error al actualizar unlock request',
                ['exception' => $e->getMessage()],
                500
            );
        }
    }


    //!LOGIN
    public function login(Request $request)
    {
        $statusResponse = $this->validateLoginStatus($request);
        $data = $statusResponse->getData();
        if ($data->status === env('RESPONSE_OK')) {
            return $this->accessToEmployee($request);
        } else {
            return $this->sendError($data->status);
        }
    }

    public function validateLoginStatus(Request $request)
    {
        $username = $request->input('username');
        $result = EmployeeService::checkLogin($username);

        return response()->json([
            'status' => $result['status'],
            'code' => $result['code'],
            'message' => $result['message'],
        ], $result['http']);
    }


    //! METODO AUXILIAR ACCESO LOGIN 

    public function accessToEmployee(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'version' => 'nullable|string',
        ]);

        $ldapAccess = false;
        $credentials = $request->only('username', 'password');
        if (!Auth::guard('web_local')->attempt($credentials)) {
            $ldapAccess = true;
            if (!Auth::attempt($credentials)) {
                return $this->sendError('Credenciales inválidas');
            }
        }
        $user = $ldapAccess
            ? auth()->user()
            : Auth::guard('web_local')->user();
        $token = $user->createToken('authToken')->accessToken;
        $filteredUser = $user->only([
            'id',
            'name',
            'username',
            'centre_id',
            'validated',
            'pending_password',
            'email',
            'dni',
            'phone',
            'category',
            'count_access',
            'unlockRequest',
            // 'img', // ← Descomenta si lo necesitas
        ]);
        $success = [
            'access_token' => $token,
            'user' => $filteredUser
        ];
        Employee::updatingAccess($user->id, env('UNLOCK'));
        Employee::updatingUnlockRequest($user->username, env('UNLOCK')); // ← Usa el método refactorizado
        Employee::updatingRegistryVersion($user->username, $request->input('version', ''));

        return $this->sendResponse($success, 'Usuario logueado!!');
    }


    //!SOLICITUD DE RECUPERACIÓN DE CONTRASEÑA DE EMPLEADOS

    public function recoveryPass(Request $request)
    {
        $employee_exists = $this->validateLoginStatus($request);
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
