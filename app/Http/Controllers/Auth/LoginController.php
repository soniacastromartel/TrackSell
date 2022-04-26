<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth; 
use App\Role; 
use Adldap; 
use Illuminate\Validation\ValidationException;
use App\Employee;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $guardPath; 

    public function username()
    {   
        return 'username';
    }
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        
        //Control user expired in LDAP
        /*$user = $request->all(); 
        
        if (!$userLdap->isActive()){
            return redirect()->to('login')->with('error', 'Error, usuario dado de baja');
        } */
       
        $user = $request->all(); 

        //$userLdap = Adldap::getDefaultProvider()->search()->find($user['username']);
        $employee = Employee::select('employees.id'
                            ,'employees.username'
                            ,'employees.name'
                            ,'employees.cancellation_date'
                            )
                    //->whereNull('employees.cancellation_date')
                    ->where('employees.username' ,'=', $user['username'])
                    ->get();
        $employee = $employee->toArray();
        //1.- Comprobaciones de restriccion de acceso de usuario

            //1.1.- Usuario en LDAP inactivo
            //1.2.- Usuario en sistema, con fecha de cancelacion

        if ($user['username'] == "admin") {
            $userLdapActive = true; 
        } else {
            $userLdap =  Adldap::getDefaultProvider()->search()->find($user['username']);
            $userLdapActive = true; 
        }

        if ((isset($employee) && empty($employee->cancellation_date))  && $this->attemptLogin($request) && ($userLdapActive)) 
        {
            $guard = $this->guardPath; 
            $userAuthenticated = $guard->user();
            session()->put('user', $userAuthenticated); 
            session()->save();
            $rol = Role::find($userAuthenticated->rol_id);
            $userAuthenticated->levelAccess = $rol['level_id'];

            // The user is being remembered...
            return $this->sendLoginResponse($request);
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
    }


    protected function guard()
    {
        if (Auth::guard('web_local')->attempt(request()->only('username', 'password'))) {
            $this->guardPath = Auth::guard('web_local');
        } else {
            $this->guardPath = Auth::guard();
        }
        return $this->guardPath; 
    }

}
