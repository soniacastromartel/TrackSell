<?php

namespace App;
use App\Services\CenterService;
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

    protected $hidden = ['password', 'remember_token'];

    protected $fillable = [
        'name',
        'username',
        'username_temp',
        'cancellation_date',
        'password',
        'centre_id',
        'rol_id',
        'validated',
        'pending_password',
        'dni',
        'phone',
        'mobile_phone',
        'email',
        'category',
        'user_cancellation_date',
        'centro_a3',
        'baja_a3',
        'cod_employee',
        'cod_business',
        'nombre_a3',
        'force_centre_id',
        'updateRequest',
        'excludeRanking',
        'unlockRequest',
        'img',
        'job_category_id'
    ];

    // Relación con Centre
    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    // Relación con Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    // Password hashing (mejor que encriptar)
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = Hash::make($password);
        }
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    // Método para encontrar empleado por campo
    public static function findByField(string $field, $value)
    {
        return self::whereNull('cancellation_date')
            ->where($field, $value)
            ->first();
    }

    public function getPrescriptorsCenters()
    {
        $service = CenterService::getPrescriptorCenters($this);
        return $service;
    }

    // Método para obtener empleados activos
    public static function getEmployeesActive()
    {
        return self::where(function ($query) {
            $query->where('cancellation_date', '>', now())
                ->orWhereNull('cancellation_date');
        })->orderBy('name')->get();
    }

    // Método para obtener supervisores activos
    public static function getActiveSupervisors($centre_id = null)
    {
        $query = self::select('employees.id', 'employees.name', 'employees.username', 'centres.name as centre')
            ->join('roles', 'employees.rol_id', '=', 'roles.id')
            ->join('centres', 'employees.centre_id', '=', 'centres.id')
            ->whereNull('employees.cancellation_date')
            ->where('roles.name', 'SUPERVISOR');

        if ($centre_id) {
            $query->where('centres.id', $centre_id);
        }

        return $query->orderBy('employees.name')->get();
    }

    // Métodos de actualización
    public static function updateEmployeeByField(string $field, $value, callable $callback): bool
    {
        $employee = self::findByField($field, $value);
        if (!$employee) {
            return false;
        }

        $callback($employee);

        return $employee->save();
    }

    public static function updatingAccess(int $employee_id, int $count): bool
    {
        return self::updateEmployeeByField('id', $employee_id, function ($employee) use ($count) {
            $employee->count_access = $count;
        });
    }

    public static function updatingUnlockRequest(string $username, int $value = 0): bool
    {
        return self::updateEmployeeByField('username', $username, function ($employee) use ($value) {
            $employee->unlockRequest = $value;
        });
    }

    public static function updatingRegistryVersion(string $username, string $version): bool
    {
        return self::updateEmployeeByField('username', $username, function ($employee) use ($version) {
            $employee->version = $version;
        });
    }

    public static function findActiveByUsername(string $username): ?self
    {
        return self::whereRaw("BINARY username = ?", [$username])
            ->where(function ($query) {
                $query->where('cancellation_date', '>', now()->toDateString())
                    ->orWhereNull('cancellation_date');
            })
            ->first();
    }

}

