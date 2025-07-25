<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Http\Request;


class Tracking extends Model
{
    protected $fillable = [
        'id',
        'name',
        'hc',
        'patient_name',
        'patient_email',
        'started_date',
        'started_user_id',
        'apointment_done',
        'apointment_user_id',
        'apointment_date',
        'service_done',
        'service_user_id',
        'service_date',
        'invoiced_done',
        'invoiced_user_id',
        'invoiced_date',
        'validation_done',
        'validation_user_id',
        'validation_date',
        'cancellation_date',
        'cancellation_reason',
        'cancellation_user_id',
        'service_id',
        'centre_id',
        'centre_employee_id',
        'employee_id',
        'observations',
        'quantity',
        'dni',
        'phone',
        'paid_done',
        'paid_date',
        'paid_user_id',
        'state',
        'state_date',
        'discount',
        'department'
    ];

    //!RELATIONS

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

     public function department()
    {
        return $this->belongsTo(Department::class, 'department');
    }

    public function scopeGetPatients()
    {

        $services = DB::table('trackings')
            ->select('patient_name')
            ->whereNull('cancellation_date')
            ->orderBy('patient_name')
            ->distinct()
            ->get();
        return $services;
    }


    public function scopeGetTrackings()
    {
        $query = DB::table('trackings')
            ->select(
                'centres.id as centre_id',
                'centres.name as centre',
                'employees.name as employee',
                'trackings.id',
                'trackings.hc',
                'trackings.patient_name',
                'trackings.state',
                'trackings.state_date',
                'services.name as service',
                'trackings.started_date',
                'apointment_date',
                'service_date',
                'invoiced_date',
                'validation_date',
                'trackings.cancellation_date'
            )
            ->join('services', 'services.id', '=', 'service_id')
            ->join('employees', 'employees.id', '=', 'employee_id')
            ->join('centres', 'centres.id', '=', 'centre_employee_id');
        return $query;
    }

    public function scopeGetNonCancelledTrackings()
    {
        $query = DB::table('trackings')
            ->select(
                'centres.id as centre_id',
                'centres.name as centre',
                'employees.name as employee',
                'employees.dni as dni',
                'trackings.id',
                'trackings.hc',
                'trackings.patient_name',
                'trackings.state',
                'trackings.state_date',
                'services.name as service',
                'trackings.started_date',
                'apointment_date',
                'service_date',
                'invoiced_date',
                'validation_date',
                'trackings.cancellation_date'
            )
            ->join('services', 'services.id', '=', 'service_id')
            ->join('employees', 'employees.id', '=', 'employee_id')
            ->join('centres', 'centres.id', '=', 'centre_employee_id')
            ->whereNull('trackings.cancellation_date');
        return $query;
    }

    public function scopeGetCancelledTrackings()
    {
        $query = DB::table('trackings')
            ->select(
                'centres.id as centre_id',
                'centres.name as centre',
                'employees.name as employee',
                'employees.dni as dni',
                'trackings.id',
                'trackings.hc',
                'trackings.cancellation_reason',
                'trackings.patient_name',
                'trackings.state',
                'trackings.state_date',
                'services.name as service',
                'trackings.started_date',
                'apointment_date',
                'service_date',
                'invoiced_date',
                'validation_date',
                'trackings.cancellation_date'
            )
            ->join('services', 'services.id', '=', 'service_id')
            ->join('employees', 'employees.id', '=', 'employee_id')
            ->join('centres', 'centres.id', '=', 'centre_employee_id')
            ->whereNotNull('trackings.cancellation_date')
            ->get();
        return $query;
    }

    /**
     * Gets and counts trancking by department
     *
     * @param mixed $startDate
     * @param mixed $endDate
     * @return Tracking[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getTrackingGroupByDepartments($startDate = null, $endDate = null)
    {
        $query = self::select('department', DB::raw('count(*) as total'))
            ->whereNull('cancellation_date')
            ->groupBy('department')
            ->orderBy('department');

        if ($startDate && $endDate) {
            $query->whereBetween('started_date', [$startDate, $endDate]);
        }

        return $query->get();
    }

    /**
     * Gets and counts trancking by centre, including centre_name and total
     * @param mixed $startDate
     * @param mixed $endDate
     * @return Tracking[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getTrackingGroupByCenters($startDate = null, $endDate = null)
    {
        $query = self::select('centres.id as centre_id', 'centres.name as centre_name', DB::raw('count(*) as total'))
            ->join('centres', 'centres.id', '=', 'trackings.centre_id')
            ->whereNull('trackings.cancellation_date')
            ->groupBy('centres.id', 'centres.name')
            ->orderBy('centres.name');

        if ($startDate && $endDate) {
            $query->whereBetween('trackings.started_date', [$startDate, $endDate]);
        }

        return $query->get();
    }



    public function scopeCheckDate($query, $trackingDate)
    {
        $result = true;
        $message = '';
        $currentMonth = date('m');
        $currentDay = date('d');
        $year = date('Y');

        $beforeYear = $year;
        $nextYear = $year;
        $beforeMonth = $currentMonth - 1;
        $nextMonth = $currentMonth + 1;
        if ($currentMonth == 1) {
            $beforeMonth = 12;
            $beforeYear = $year - 1;
        }
        if ($currentMonth == 12) {
            $nextMonth = 1;
            $nextYear = $year + 1;
        }

        if ($currentDay <= 20) {
            $initPeriod = $beforeYear . '-' . str_pad($beforeMonth, 2, "0", STR_PAD_LEFT) . '-' . env('START_DAY_PERIOD');
            $endPeriod = $year . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');
        } else {
            $initPeriod = $year . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . env('START_DAY_PERIOD');
            $endPeriod = $nextYear . '-' . str_pad($nextMonth, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');
        }
        $dias = array("domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado");
        $endPreviousPeriod = date("d-m-Y", strtotime($initPeriod . "- 1 days"));
        $endDayName = $dias[date("w", strtotime($endPreviousPeriod))];
        $controlFecha = true;
        $this->user = session()->get('user');
        if ($currentDay > 20) {
            if ($currentDay == 21) {
                $controlFecha = false;
            }
            if ($endDayName == "viernes" && $currentDay - 21 == 2) { //Cae el 20 en viernes, fecha 21 es sábado
                $controlFecha = false;
            }
            if ($endDayName == "sábado" && $currentDay - 21 == 1) { //Cael el 20 en sábado, fecha 21 es domingo
                $controlFecha = false;
            }
            if ($endDayName == "domingo" && $currentDay - 21 == 1) {
                $controlFecha = false;
            }
        }
        if (!empty($this->user) && $this->user->rol_id == 1) { //ROL_ID admin se permiten cambios
            $controlFecha = false;
        }
        if ($controlFecha === true && $trackingDate != null && ($trackingDate < $initPeriod || $trackingDate > $endPeriod)) {
            $result = false;
            $message = 'Fecha fuera de periodo de corte, corrijalo por favor';
        }
        return ['result' => $result, 'message' => $message];
    }
}
