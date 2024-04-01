<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class Employee extends Authenticatable
{
    use Notifiable, HasApiTokens;
    //use Notifiable;
    //protected $primaryKey = 'id';
    // protected $guarded = ['login'];
    protected $hidden = [
        'password', 'remember_token'
    ];
    protected $fillable = [
        'name'
        , 'username'
        , 'username_temp'
        , 'cancellation_date'
        , 'password'
        , 'centre_id'
        , 'rol_id'
        , 'validated'
        , 'pending_password'
        , 'username_temp'
        , 'dni'
        , 'phone'
        , 'mobile_phone'
        , 'email'
        , 'category'
        , 'user_cancellation_date'
        , 'centro_a3'
        , 'baja_a3'
        , 'cod_employee'
        , 'cod_business'
        , 'nombre_a3'
        , 'force_centre_id'
        , 'updateRequest'
        , 'excludeRanking'
        , 'unlockRequest'
        , 'img'
    ];

    //ldap AUTHENTICATABLE
    // public static function usePasswordStrategy(callable $strategy)
    // {
    //     static::$passwordStrategy = $strategy;
    // }
    // public function setPasswordAttribute($password)
    // {
    //     if ( $password !== null & $password !== "" )
    //     {
    //         $this->attributes['password'] = Crypt::encryptString($password); 
    //     }
    // }

    // public function getPasswordAttribute($password)
    // {
    //     if (!empty($password)) {
    //         return Crypt::decryptString($password);
    //         //return $password; 
    //     }
    // }

    public function scopeGetPrescriptorCenter()
    {

        $employee = $this->toArray();
        $centres = [];

        $principalCentre = Centre::where(['id' => $employee['centre_id']])->first();
        if (empty($principalCentre)) {
            return $centres;
        }
        $centres[] = [
            'centre'             => $principalCentre->label, 'centre_id'        => $principalCentre->id, 'centre_address'   => $principalCentre->address, 'centre_phone'     => $principalCentre->phone, 'centre_email'     => $principalCentre->email, 'timetable'        => $principalCentre->timetable, 'island'           => $principalCentre->island, 'image'            => env('BASE_API_URL') . $principalCentre->image, 'principal'        => true
        ];

        /** Centros de Tenerife -- CodEmpresa = 19 */
        if (!empty($principalCentre) && $principalCentre->island == 'TENERIFE') {
            $tfeCentres = Centre::where(['island' => 'TENERIFE'])
                ->whereNull('cancellation_date')
                ->get();
            foreach ($tfeCentres as $c) {
                if ($c->id != $principalCentre->id) {
                    $centres[] = [
                        'centre'           => $c->label, 'centre_id'        => $c->id, 'centre_address'   => $c->address, 'centre_phone'     => $c->phone, 'centre_email'     => $c->email, 'timetable'        => $c->timetable, 'island'           => $c->island, 'image'            => env('BASE_API_URL') . $c->image, 'principal'        => false
                    ];
                }
            }
        } else if ($principalCentre->id == env('ID_CENTRE_ICOT')  || $principalCentre->id == env('ID_CENTRE_PARQUE_LANZAROTE') || $principalCentre->id == env('ID_CENTRE_PARQUE_FUERTEVENTURA')) {

            /** Centro ICOT - Policlinico --- Puede vender desde otros centros de Gran Canaria*/
            $lpaCentres = Centre::where(['island' => 'GRAN CANARIA'])
                ->where('name', 'not like', "%HOSPITAL%")
                ->whereNull('cancellation_date')
                ->get();
            foreach ($lpaCentres as $c) {
                if ($c->id != $principalCentre->id) {
                    $centres[] = [
                        'centre'           => $c->label, 'centre_id'        => $c->id, 'centre_address'   => $c->address, 'centre_phone'     => $c->phone, 'centre_email'     => $c->email, 'timetable'        => $c->timetable, 'island'           => $c->island, 'image'            => $c->image, 'principal'        => false
                    ];
                }
            }
        }
        return $centres;
    }

    public function getAuthPassword()
    {
        return $this->password;
        //Uncomment to cypher in aes 256 
        //return Hash::make(Crypt::decryptString($this->password));
    }

    public function scopeGetEmployeesActive()
    {

        $employees = Employee::where(function ($query) {
            $query->where('cancellation_date', '>', date('Y-m-d'))
                ->orWhereNull('cancellation_date');
        })
            ->orderBy('name')->get();
        return $employees;
    }

    public function scopeGetSupervisorsActive($query, $centre_id = null)
    {

        $whereFields = "roles.name = 'SUPERVISOR'";
        if (!empty($centre_id)) {
            $whereFields .=  " and centres.id = " . $centre_id;
        }
        $employees = Employee::select(
            'employees.id',
            'employees.name',
            'employees.username',
            'centres.name as centre'
        )
            ->join('roles', 'employees.rol_id', '=', 'roles.id')
            ->join('centres', 'employees.centre_id', '=', 'centres.id')
            ->whereNull('employees.cancellation_date')
            ->whereRaw($whereFields)
            ->orderBy('employees.name')->get();

        return $employees;
    }

    /*
    * Actualizacion de conteo de accesos permitidos
    */
    public function scopeUpdatingAccess($query, $employee_id, $count)
    {
        $employee = Employee::where(['id' => $employee_id]);
        $access['count_access'] = $count;
        $resultado = false;
        if (!empty($employee)) {
            $employee->update($access);
            $resultado = true;
        }
        return $resultado;
    }
    
    //TODO -- METODO scopeUpdatingUnlock

    public function scopeUpdatingUnlockRequest($query, $username)
    {
        $employee = Employee::where(['username' => $username]);
        if (!empty($employee)) {
            $employee->update(['unlockRequest' => 0]);
            // return $this->sendResponse('', 200);
        }

        // $user       = $params['username'];
        // if (!empty($user)) {
        //     $employee = Employee::whereRaw("BINARY username = ?", [$user])->first();
        //     if (!empty($employee)) {
        //         $employee->update(['unlockRequest' => 1]);
        //         return $this->sendResponse('', 200);
        //     }
        // }
    }

    /*
    * Actualizacion de version última de la App usada
    */
    public function scopeUpdatingRegistryVersion($query, $username, $version)
    {
        $employee = Employee::where(['username' => $username]);
        $vActual['version'] = $version;

        if (!empty($employee)) {
            $employee->update($vActual);
        }
    }
}
