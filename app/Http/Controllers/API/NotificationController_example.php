<?php
namespace App\Http\Controllers\API;

use DataTables;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Centre;
use App\Service;
use App\Tracking;



class NotificationController extends BaseController
{

    public function index(Request $request)
    {

        try {
            $title = 'Avisos de supervisor';
            if ($request->ajax()) {

                $params = $request->all();
                $query  = Tracking::select(
                    'centres.id as centre_id',
                    'centres.name as centre',
                    'employees.name as employee',
                    'trackings.id',
                    'trackings.patient_name',
                    'trackings.state',
                    'trackings.started_date',
                    'trackings.cancellation_date',
                    'trackings.cancellation_reason',
                    'services.name as service',
                    'started_date',
                    'service_date',
                )
                    ->join('services', 'services.id', '=', 'service_id')
                    ->join('employees', 'employees.id', '=', 'employee_id')
                    ->join('centres', 'centres.id', '=', 'centre_employee_id');

                $currentDay   = date('d');
                $currentMonth = substr($params['date'], 0, strpos($params['date'], '/'));
                $year         = substr($params['date'], strpos($params['date'], '/') + 1);

                $beforeMonth = $currentMonth - 1;
                $nextMonth = $currentMonth + 1;
                $beforeYear  = $year;
                $nextYear  = $year;

                if ($currentMonth == 1) {
                    $beforeMonth = 12;
                    $beforeYear  = $year - 1;
                }
                if ($currentMonth >= 12) {
                    $currentMonth = 12;
                    $nextMonth = 1;
                    $nextYear  = $year + 1;
                }

                $initPeriod = $beforeYear . '-' . str_pad($beforeMonth, 2, "0", STR_PAD_LEFT) . '-' . env('START_DAY_PERIOD');
                $endPeriod  = $nextYear . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . env('END_DAY_PERIOD');

                $user = session()->get('user');
                if ($user->rol_id == '3') {
                    $query = $query->where('centres.id', $user->centre_id);
                }

                $canceledTrackings = $query
                    ->where('state', 'Cancelado')
                    ->whereNotNull('trackings.cancellation_date')


                    ->where(function ($q) use ($params, $initPeriod, $endPeriod) {

                        $q->where(function ($q2) use ($params, $initPeriod, $endPeriod) {
                            $q2->
                                // whereBetween('started_date', [$initPeriod, $endPeriod])
                                // ->orWhereBetween('service_date', [$initPeriod, $endPeriod])
                                WhereBetween('trackings.cancellation_date', [$initPeriod, $endPeriod]);
                        });
                    });
                // ->get();

                return DataTables::of($canceledTrackings)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w
                                    ->orWhere('employees.name', 'LIKE', "%$search%")
                                    ->orWhere('services.name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->make(true);
            }
            $centres = Centre::getCentresActive();

            return view('notifications', [
                'title' => $title, 'mensaje' => '',  'centres'  => $centres

            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar las notificaciones, contacte con el administrador');
        }
    }

    public function getNotifications()
    {
        try {

            $canceledTrackings = Tracking::getCancelledTrackings();
            return $canceledTrackings;
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error '. $e);
        }
    }
}
