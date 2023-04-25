<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
// use Adldap\Laravel\Facades\Adldap;

use DataTables;
use App\Tracking;
use App\Centre;
use App\Service;
use App\Employee;
use DateTime;
use DB;
use App\Exports\TrackingsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\ServicePrice;
use App\Services\TargetService;
use App\A3Centre;
use App\Exports\TrackingsValidateExport;
use App\A3Empleado;
use App\TrackingBonus;
use App\Providers\RouteServiceProvider;
use App\ValidationRrhh;
use App\Discount;
use App\RequestChange;

use Carbon\Carbon;

class TrackingController extends Controller
{
    protected $finalDetailedSales = [];


    public function __construct()
    {
        $this->user = session()->get('user');
    }

    private function getTitle($state)
    {
        $title = '';
        switch ($state) {
            case 'started':
                $title = 'Seguimiento - Inicio';
                break;

            case 'apointment':
                $title = 'Seguimiento - Citar';
                break;

            case 'service':
                $title = 'Seguimiento - Realizar Servicios';
                break;

            case 'invoiced':
                $title = 'Seguimiento - Facturar';
                break;

            case 'validation':
                $title = 'Seguimiento - Validar';
                break;
        }
        return $title;
    }

    public function index(Request $request)
    {

        $this->user = session()->get('user');
        try {
            $state = '';
            $title = 'Recomendaciones';
            if ($request->ajax()) {
                $params = $request->all();
                $currentDay   = substr($params['dateTo'], -2, strpos($params['dateTo'], '/'));
                $beforeDay = substr($params['dateFrom'], -2, strpos($params['dateFrom'], '/'));
                $currentMonth = substr($params['dateFrom'], -5,  2);
                $nextMonth = substr($params['dateTo'], -5,  2);
                $year         = substr($params['dateFrom'], 0, strpos($params['dateFrom'], '/'));
                $nextYear  = substr($params['dateTo'], 0, strpos($params['dateTo'], '/'));

                $initPeriod = $year . '-' . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . '-' . $beforeDay;
                $endPeriod  = $nextYear . '-' . str_pad($nextMonth, 2, "0", STR_PAD_LEFT) . '-' . $currentDay;

                $query = Tracking::getTrackings();
                $trackings = $query
                    //->orderByRaw($orderBy)
                    // ->whereNull('trackings.cancellation_date')
                    ->where(function ($q) use ($params, $initPeriod, $endPeriod) {
                        if (!empty($params['centre_id'])) {
                            if ($params['centre_id'] == 'SIN SELECCION') {
                                $params['centre_id'] = null;
                            } else {
                                $q->where('centres.id', $params['centre_id']);
                            }
                        }
                        if (!empty($params['employee'])) {
                            if ($params['employee'] == 'SIN SELECCION') {
                                $params['employee'] = null;
                            } else {
                                $q->where('employees.name', $params['employee']);
                            }
                        }
                        if (!empty($params['patient'])) {
                            if ($params['patient'] == 'SIN SELECCION') {
                                $params['patient'] = null;
                            } else {
                                $q->where('trackings.patient_name', $params['patient']);
                            }
                        }
                        if (!empty($params['service'])) {
                            if ($params['service'] == 'SIN SELECCION') {
                                $params['service'] = null;
                            } else {
                                $q->where('services.name', $params['service']);
                            }
                        }
                        if (!empty($params['state'])) {
                            if ($params['state'] == 'Cancelado') {
                                $q->whereNotNull('trackings.cancellation_date')
                                    ->whereBetween('trackings.cancellation_date', [$initPeriod, $endPeriod]);
                            } else  if ($params['state'] == 'SIN SELECCION') {
                                $params['state'] = null;
                            } else {
                                $q->where('trackings.state', $params['state'])
                                    ->whereNull('trackings.cancellation_date');
                            }
                        }
                        $q->where(function ($q2) use ($params, $initPeriod, $endPeriod) {
                            $q2
                                ->whereBetween('trackings.state_date', [$initPeriod, $endPeriod])
                                ->orWhereBetween('trackings.started_date', [$initPeriod, $endPeriod])
                                ->orWhereBetween('trackings.validation_date', [$initPeriod, $endPeriod]);
                        });
                    });

                // $trackings = $trackings->toArray();
                return DataTables::of($trackings)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhere('centres.name', 'LIKE', "%$search%")
                                    ->orWhere('employees.name', 'LIKE', "%$search%")
                                    ->orWhere('services.name', 'LIKE', "%$search%")
                                    ->orWhere('trackings.patient_name', 'LIKE', "%$search%")
                                    ->orWhere('trackings.state', 'LIKE', "%$search%")
                                    ->orWhere('trackings.hc', 'LIKE', "%$search%")
                                    // ->orderBy('trackings.state_date')
                                ;
                            });
                        }
                    })

                    ->addColumn('action', function ($tracking) {
                        $btn = '';
                        if ($tracking->state != env('STATE_VALIDATE') && $tracking->state != env('STATE_PAID') && $tracking->state != env('STATE_CANCELLED')) {

                            $state = Service::getStateService($tracking->state);

                            $btn .= '<div class="col-md-12">';
                            $btn .= '<a href="edit/' . $state . '/' . $tracking->id . '" class="btn btn-warning a-btn-slide-text btn-sm">Editar</a>';
                            $btn .= '</div>';
                            $trackingDate =  date('Y-m-d');

                            $state = substr($state, 0, strpos($state, "_"));
                            if ($tracking->state == env('STATE_PENDING')) { //PENDIENTE
                                // $trackingDate = isset($tracking->started_date) ? date('Y-m-d', strtotime($tracking->started_date)) :  date('Y-m-d');
                                $btn .= '<div class="col-md-6" >';
                                $btn .= '<input style="resize:horizontal; width: 120px;" type="date" id="tracking_date_' . $tracking->id . '" name="tracking_date" max="3000-12-31" 
                                min="1000-01-01" value="' . $trackingDate . '" class="form-control"></input>';
                                $btn .= '</div>';
                                $btn .= '<div class="col-md-2">';
                                $fnCall = 'updateDateTracking(\'' . $state . '\',' . $tracking->id . ',0 )';
                                $btn .= '<a onclick="' . $fnCall . '" class="btn btn-success a-btn-slide-text btn-sm">Citar</a>';
                                $btn .= '</div></div>';
                            }
                            if ($tracking->state == env('STATE_APOINTMENT')) { //CITADOS
                                // $trackingDate = isset($tracking->apointment_date) ? date('Y-m-d', strtotime($tracking->apointment_date)) :  date('Y-m-d');
                                $btn .= '<div class="col-md-6">';
                                $btn .= '<input style="resize:horizontal; width: 160px;" type="date" id="tracking_date_' . $tracking->id . '" name="tracking_date" max="3000-12-31" 
                                min="1000-01-01" value="' . $trackingDate . '" class="form-control"></input>';
                                $btn .= '</div>';
                                $btn .= '</div>';
                                $btn .= '<div class="row col-md-12">';
                                $btn .= '<div class="col-md-6 px-2">';
                                $fnCall = 'updateDateTracking(\'' . $state . '\',' . $tracking->id . ',0 )';
                                $btn .= '<a onclick="' . $fnCall . '" class="btn btn-success a-btn-slide-text btn-sm">Realizar</a>';
                                $btn .= '</div>';
                                $btn .= '<div class="col-md-6">';
                                $fnCall = 'updateDateTracking(\'' . $state . '\',' . $tracking->id . ',1 )';
                                $btn .= '<a onclick="' . $fnCall . '" class="btn btn-red-icot a-btn-slide-text btn-sm">Reiniciar</a>';
                                $btn .= '</div></div>';
                            }
                            if ($tracking->state == env('STATE_SERVICE')) { //REALIZADOS
                                // $trackingDate = isset($tracking->service_date) ? date('Y-m-d', strtotime($tracking->service_date)) :  date('Y-m-d');
                                $btn .= '<div class="col-md-6">';
                                $btn .= '<input style="resize:horizontal; width: 160px;" type="date" id="tracking_date_' . $tracking->id . '" name="tracking_date" max="3000-12-31" 
                                min="1000-01-01" value="' . $trackingDate . '" class="form-control"></input>';
                                $btn .= '</div>';
                                $btn .= '</div>';
                                $btn .= '<div class="row col-md-12">';
                                $btn .= '<div class="col-md-6 px-3">';
                                $fnCall = 'updateDateTracking(\'' . $state . '\',' . $tracking->id . ',0 )';
                                $btn .= '<a onclick="' . $fnCall . '" class="btn btn-success a-btn-slide-text btn-sm">Facturar</a>';
                                $btn .= '</div>';
                                $btn .= '<div class="col-md-6">';
                                $fnCall = 'updateDateTracking(\'' . $state . '\',' . $tracking->id . ',1 )';
                                $btn .= '<a onclick="' . $fnCall . '" class="btn btn-red-icot a-btn-slide-text btn-sm">Citar</a>';
                                $btn .= '</div></div>';
                            }
                            if ($tracking->state == env('STATE_INVOICED')) { //FACTURADOS
                                // $trackingDate = isset($tracking->invoiced_date) ? date('Y-m-d', strtotime($tracking->invoiced_date)) :  date('Y-m-d');
                                $btn .= '<div class="col-md-4">';
                                $btn .= '<input style="resize:horizontal; width: 160px;" type="date" id="tracking_date_' . $tracking->id . '" name="tracking_date" max="3000-12-31" 
                                min="1000-01-01" value="' . $trackingDate . '" class="form-control"></input>';
                                $btn .= '</div>';
                                $btn .= '</div>';
                                $btn .= '<div class="row col-md-12">';
                                $btn .= '<div class="col-md-6 px-2">';
                                $fnCall = 'updateDateTracking(\'' . $state . '\',' . $tracking->id . ',0 )';
                                $btn .= '<a onclick="' . $fnCall . '" class="btn btn-success a-btn-slide-text btn-sm">Validar</a>';
                                $btn .= '</div>';
                                $btn .= '<div class="col-md-6">';
                                $fnCall = 'updateDateTracking(\'' . $state . '\',' . $tracking->id . ',1 )';
                                $btn .= '<a onclick="' . $fnCall . '" class="btn btn-red-icot a-btn-slide-text btn-sm">Realizar</a>';
                                $btn .= '</div></div>';
                            }
                        }

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            $centres = Centre::getCentresActive();
            $services = Service::orderBy('name')->get();
            $patients = Tracking::getPatients();
            $employees = Employee::getEmployeesActive();
            $states = array(
                (object)
                [
                    'nombre' => env('STATE_PENDING'),
                    'texto' => 'pending'
                ],
                (object)
                [
                    'nombre' => env('STATE_APOINTMENT'),
                    'texto' => 'apointment'
                ],
                (object)
                [
                    'nombre' => env('STATE_SERVICE'),
                    'texto' => 'service'
                ],
                (object)
                [
                    'nombre' => env('STATE_INVOICED'),
                    'texto' => 'invoiced'
                ],
                (object)
                [
                    'nombre' => env('STATE_VALIDATE'),
                    'texto' => 'validation'
                ],
                (object)
                [
                    'nombre' => env('STATE_PAID'),
                    'texto' => 'paid'
                ],
                (object)
                [
                    'nombre' => env('STATE_CANCELLED'),
                    'texto' => 'cancellation'
                ]

            );


            return view('tracking.index', [
                'title' => $title, 'mensaje' => '',  'centres'  => $centres,  'states'   => $states, 'employees'  => $employees, 'services'  => $services, 'patients'  => $patients,
                'user'      => $this->user,

            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar seguimiento, contacte con el administrador');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        try {
            $centres = Centre::getCentresActive();
            $title = $this->getTitle('started');

            $services = Service::getServicesActive();
            $disabledService = false;

            $employees = Employee::getEmployeesActive();
            $discounts = Discount::whereNull('cancellation_date')->get();

            return view('tracking.create', [
                'title'            => $title, 'centres'         => $centres, 'services'        => $services, 'employees'       => $employees, 'disabledService' => $disabledService, 'discounts'       => $discounts
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar formulario de nuevo seguimiento, contacte con el administrador');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try {
            $user = session()->get('user');
            $request->validate([
                'employee_id'           => 'required',
                'patient_name'          => 'required',
                'centre_id'             => 'required',
                'centre_employee_id'    => 'required',
                'service_id'            => 'required',
                'tracking_date'         => 'before:tomorrow'
            ]);

            $params = [];
            $params = $request->all();
            $params['started_user_id'] = $user->id;
            $params['apointment_done'] = 0;
            $params['started_date'] = $params['tracking_date'];
            $params['state_date']   = $params['tracking_date'];

            $service = DB::table('services')->where('name', $params['service_name'])->first();
            $params['service_id']  = $service->id;

            if (empty($params['hc']) && empty($params['dni']) && empty($params['phone'])) {
                throw new \Illuminate\Validation\ValidationException('¡Error!, tipo de identificación vacía');
            }

            $error = $this->checkServiceCentre($params, 'started');
            if ($error) {
                return redirect()->action('TrackingController@index', 'started')
                    ->with('error', 'El servicio y centro elegidos no están disponibles');
            } else {
                $tracking_id = Tracking::create($params)->id;
                return redirect()->action('TrackingController@index', 'started')

                    ->with('success', 'Seguimiento creado correctamente');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (gettype($e->validator) == 'string') {
                return back()->with('error', $e->validator);
            } else {
                return back()->with('error', 'Ha ocurrido un error en validación de formulario');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al crear seguimiento, contacte con el administrador');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($state, $id)
    {
        //
        try {
            $tracking = Tracking::find($id);
            $centres = Centre::getCentresActive();

            $services = Service::getServicesActive(null, true);
            $employees = Employee::getEmployeesActive();
            $employee = Employee::find($tracking->employee_id);
            $currentDate = date('Y-m-d H:i:s');
            $disabledService = false;
            foreach ($services as $service) {
                if ($service->id ==  $tracking->service_id) {
                    if (!empty($service->cancellation_date) && $service->cancellation_date <= $currentDate) {
                        $disabledService = true;
                    }
                }
            }

            $discounts = Discount::whereNull('cancellation_date')->get();

            return view('tracking.edit', [
                'title'           => 'Modificar recomendación', 'tracking'        => $tracking, 'centres'         => $centres, 'services'        => $services, 'employees'       => $employees, 'state'           => $state, 'employee'        => $employee, 'tracking_date'   => $tracking[$state], 'discounts'       => $discounts, 'disabledService' => $disabledService
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar seguimiento para editar, contacte con el administrador');
        }
    }

    /**
     * Cambio de estado de seguimiento
     */
    public function update(Request $request, $state, $id)
    {
        //
        try {
            \Log::debug($request->all());
            $validator = Validator::make($request->all(), [
                'centre_id'          => 'required',
                'centre_employee_id' => 'required',
                'service_id'         => 'required',
                'employee_id'        => 'required',
                'patient_name'       => 'required'
            ]);


            if ($validator->fails()) {
                \Log::debug($validator->errors());
                return redirect()->action('TrackingController@index')
                    ->with('error', 'Ha ocurrido un error');
            } else {

                $tracking = Tracking::find($id);
                $mensajeError = 'Error fecha, ';
                switch ($state) {
                    case 'started':
                        if (!empty($tracking['apointment_date'])) {
                            $mensajeError .= 'posterior a fecha de cita';
                            $request->validate([
                                'tracking_date' => 'before_or_equal:' . $tracking['apointment_date'] . '|before:tomorrow'
                            ]);
                        } else {
                            $request->validate([
                                'tracking_date' => 'before:tomorrow'
                            ]);
                        }
                        break;
                    case 'apointment':
                        if (!empty($tracking['started_date'])) {
                            $mensajeError .= 'anterior a fecha de inicio';
                            $request->validate([
                                'tracking_date' => 'after_or_equal:' . $tracking['started_date'] . '|before:tomorrow'
                            ]);
                        } else {
                            $request->validate([
                                'tracking_date' => 'before:tomorrow'
                            ]);
                        }
                        break;
                    case 'service':
                        if (!empty($tracking['apointment_date'])) {
                            $mensajeError .= 'anterior a fecha de cita';
                            $request->validate([
                                'tracking_date' => 'after_or_equal:' . $tracking['apointment_date'] . '|before:tomorrow'
                            ]);
                        } else {
                            $request->validate([
                                'tracking_date' => 'before:tomorrow'
                            ]);
                        }
                        break;
                    case 'invoiced':
                        if (!empty($tracking['service_date'])) {
                            $mensajeError .= 'anterior a fecha de realizar servicio';
                            $request->validate([
                                'tracking_date' => 'after_or_equal:' . $tracking['service_date'] . '|before:tomorrow'
                            ]);
                        } else {
                            $request->validate([
                                'tracking_date' => 'before:tomorrow'
                            ]);
                        }
                        break;
                    case 'validation':
                        if (!empty($tracking['service_date'])) {
                            $mensajeError .= 'anterior a fecha de facturación';
                            $request->validate([
                                'tracking_date' => 'after_or_equal:' . $tracking['invoiced_date'] . '|before:tomorrow'
                            ]);
                        } else {
                            $request->validate([
                                'tracking_date' => 'before:tomorrow'
                            ]);
                        }
                        break;
                }

                $params = $request->all();
                $params[$state . '_date'] = $params['tracking_date'];
                if ($params['discount'] == -1) {
                    unset($params['discount']);
                }
                $error = $this->checkServiceCentre($params, $state);
                if ($error) {
                    return redirect()->action('TrackingController@index', 'started')
                        ->with('error', 'El servicio y centro elegidos no están disponibles');
                } else {
                    $tracking->update($params);
                    return redirect()->action('TrackingController@index', $state)

                        ->with('success', 'Recomendacion actualizada correctamente');
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', $mensajeError . ' o fecha a futuro');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al actualizar seguimiento , contacte con el administrador');
        }
    }

    /** 
     * Metodo validar seguimiento, listado Validar RRHH
     * 
     */
    public function updatePaidState(Request $request)
    {
        try {
            $mensaje = "";
            $user = session()->get('user');
            session()->reflash();

            $mensajeError = '';
            $paramsRequest = $request->all();

            if ($paramsRequest['back'] == 0) {
                $params = [
                    'paid_date'     => date('Y-m-d', strtotime($paramsRequest['trackingDate'])), 'paid_user_id' => $user->id, 'paid_done'    => true, 'state'        => env('STATE_PAID'), 'state_date'   => date('Y-m-d', strtotime($paramsRequest['trackingDate']))
                ];
                $paramUpdateVal = ['paid_date' => $paramsRequest['trackingDate']];
            } else {
                $params = [
                    'paid_date'     => null, 'paid_user_id' => null, 'paid_done'    => false, 'state'        => env('STATE_VALIDATE')
                ];
                $paramUpdateVal = ['paid_date' => null];
            }


            /** Contemplar casos de baja, antes de fecha fin de corte */
            $tIds = explode('-', $paramsRequest['trackingIds']);
            $validateFechas = null;
            foreach ($tIds as $tId) {
                if (!empty($tId)) {
                    $tracking = Tracking::find($tId);
                    if ($request->back == 1) {
                        $paramsRequest['trackingDate']  = $tracking->validation_date;
                    }
                    /** Validar que las fechas, son las que están dentro del corte actual */
                    $validateFechas = Tracking::checkDate($paramsRequest['trackingDate']);
                    if ($validateFechas['result'] === false) {
                        session()->flash('error', $validateFechas['message']);
                        return response()->json([
                            'success' => false, 'url'    => '/tracking/indexvalidation', 'mensaje' => $validateFechas['message']
                        ], 400);
                    }
                    if ($paramsRequest['back'] == 1) {
                        $params['state_date']   = $tracking->validation_date;
                    }
                    $tracking->update($params);
                }
            }

            if ($paramsRequest['supervisor'] == 1) {
                $params['employee_id'] = $paramsRequest['employee_id'];
                $params['total_income'] = $paramsRequest['totalIncome'];
                $params['month_year'] = $paramsRequest['monthYear'];
                $tbonus = TrackingBonus::where(['employee_id' => $params['employee_id']]);

                if ($paramsRequest['back'] == 0) {
                    if (empty($tbonus->get()->toArray())) {
                        $tracking_id = TrackingBonus::create($params)->id;
                    }
                } else {
                    $tbonus->delete();
                }
            }
            $validateEmployee = ValidationRrhh::where('employee_id', '=', $paramsRequest['employee_id']);
            $validateEmployee->update($paramUpdateVal);
            $mensaje = 'Realizado correctamente';

            return response()->json([
                'success' => true, 'mensaje' => $mensaje
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $mensajeError = "Error de validacion: ";
            foreach (collect($e->validator->errors())  as $key => $value) {
                $mensajeError .= $value[0];
            }
            session()->flash('error', $mensajeError);
            return response()->json([
                'success' => false, 'url'    => '/tracking/indexvalidation', 'mensaje' => $mensajeError
            ], 400);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al actualizar estado de seguimiento , contacte con el administrador');
        }
    }

    public function updateState($state, $tracking_id, $tracking_date,  $back = false)
    {
        try {
            if ($state != 'paid') {
                $tracking = Tracking::find($tracking_id);
            }
            $mensaje = "";
            $user = session()->get('user');
            session()->reflash();

            $mensajeError = '';

            /** Validar que las fechas, son las que están dentro del corte actual */
            $trackingDate = isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d');
            $validateFechas = Tracking::checkDate($trackingDate);
            if ($validateFechas['result'] === false) {
                session()->flash('error', $validateFechas['message']);
                return response()->json([
                    'success' => false, 'url'    => $state != 'paid' ? '/tracking/index_' . $state : '/tracking/indexvalidation', 'mensaje' => $validateFechas['message']
                ], 400);
            }
            switch ($state) {
                case 'started':
                    // if ($back) {
                    //     $params = [
                    //         'apointment_date'     => null
                    //         ,'apointment_user_id' => null
                    //         ,'apointment_done'    => 0
                    //     ]; 
                    //     $mensaje = 'Recomendacion iniciada correctamente';
                    // } else {
                    $params = [
                        'apointment_date'     => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'apointment_user_id' => $user->id, 'apointment_done'    => true, 'service_done'       => 0, 'state'              => env('STATE_APOINTMENT'), 'state_date' => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d')
                    ];
                    $mensaje = 'Recomendacion citada correctamente';
                    $responseUrl = '/tracking/index_apointment';
                    //}
                    // $validator = Validator::make($request, [
                    //     'tracking_date' => 'after_or_equal:'.$tracking['started_date'].'|before:tomorrow'
                    // ], $messages = [
                    //     'before' => 'Fecha a futuro, no se permite selección',
                    //     'after_or_equal' => 'La fecha es anterior a fecha de inicio',
                    //     'before_or_equal' => 'La fecha es posterior a fecha de inicio'
                    // ])->validate();
                    break;

                case 'apointment':
                    if ($back) {
                        $params = [
                            'apointment_date'     => null, 'apointment_user_id' => null, 'apointment_done'    => 0, 'started_date'       => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'started_user_id'    => $user->id, 'state'              => env('STATE_PENDING'), 'state_date'         => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'state_date' => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d')
                        ];
                        $mensaje = 'Recomendacion citada correctamente';
                        $responseUrl = '/tracking/index_apointment';
                    } else {
                        $params = [
                            'service_date'     => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'service_user_id' => $user->id, 'service_done'    => true, 'invoiced_done'   => 0, 'state'           => env('STATE_SERVICE'), 'state_date'      => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'state_date' => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d')
                        ];
                        $responseUrl = '/tracking/index_service';
                        $mensaje = 'Recomendacion realizado servicio correctamente';
                    }
                    // $validator = Validator::make($request, $rules, $messages = [
                    //     'before' => 'Fecha a futuro, no se permite selección',
                    //     'after_or_equal'  => 'La fecha es anterior a fecha de cita',
                    //     'before_or_equal' => 'La fecha es posterior a fecha de cita'
                    // ])->validate();
                    break;

                case 'service':
                    if ($back) {
                        $params = [
                            'service_date'        => null, 'service_user_id'    => null, 'service_done'       => 0, 'apointment_date'     => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'apointment_user_id'  => $user->id, 'state'               => env('STATE_APOINTMENT'), 'state_date'          => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'state_date' => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d')
                        ];
                        $rules = ['tracking_date' => 'before_or_equal:' . $tracking['service_date'] . '|before:tomorrow'];
                        $mensaje = 'Recomendacion realizado servicio correctamente';
                        $responseUrl = '/tracking/index_service';
                    } else {
                        $params = [
                            'invoiced_date'     => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'invoiced_user_id' => $user->id, 'invoiced_done'    => true, 'validation_done'  => 0, 'state'            => env('STATE_INVOICED'), 'state_date' => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d')
                        ];
                        $mensaje = 'Recomendacion facturada correctamente';
                        $responseUrl = '/tracking/index_invoiced';
                    }
                    // $validator = Validator::make($request, $rules, $messages = [
                    //     'before' => 'Fecha a futuro, no se permite selección',
                    //     'after_or_equal' => 'La fecha es anterior a fecha de realizacion de servicio',
                    //     'before_or_equal' => 'La fecha es posterior a fecha de realizacion de servicio'
                    // ])->validate();
                    break;

                case 'invoiced':
                    if ($back) {
                        $params = [
                            'invoiced_date'     => null, 'invoiced_user_id' => null, 'invoiced_done'    => 0, 'service_date'     => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'service_user_id'  => $user->id, 'state'            => env('STATE_SERVICE'), 'state_date' => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d')
                        ];
                        $mensaje = 'Recomendacion facturada correctamente';
                        $responseUrl = '/tracking/index_invoiced';
                    } else {
                        $params = [
                            'validation_date'     => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d'), 'validation_user_id' => $user->id, 'validation_done'    => true, 'state'              => env('STATE_VALIDATE'), 'state_date' => isset($tracking_date) ? date('Y-m-d', strtotime($tracking_date)) : date('Y-m-d')
                        ];
                        $mensaje = 'Recomendacion validada correctamente';
                        $responseUrl = '/tracking/index_validation';
                    }
                    // $validator = Validator::make($request, [
                    //     'tracking_date' => 'after_or_equal:'.$tracking['invoiced_date'].'|before:tomorrow'
                    // ], $messages = [
                    //     'before' => 'Fecha a futuro, no se permite selección',
                    //     'after_or_equal' => 'La fecha es anterior a fecha de facturación de servicio',
                    //     'before_or_equal' => 'La fecha es posterior a fecha de facturacion de servicio'
                    // ])->validate();
                    break;
            }
            $tracking->update($params);
            return response()->json([
                'success' => true, 'url'    => $responseUrl, 'mensaje' => $mensaje
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $mensajeError = "Error de validacion: ";
            foreach (collect($e->validator->errors())  as $key => $value) {
                $mensajeError .= $value[0];
            }
            session()->flash('error', $mensajeError);
            return response()->json([
                'success' => false, 'url'    => '/tracking/index_' . $state, 'mensaje' => $mensajeError
            ], 400);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al actualizar estado de seguimiento , contacte con el administrador');
        }
    }

    public function deleteForm()
    {
        try {
            $centres = Centre::getCentresActive();
            $services = Service::getServicesActive();
            $employees = Employee::getEmployeesActive();

            return view('tracking.delete', [
                'title'    => 'Borrar Seguimiento', 'centres'  => $centres, 'services' => $services, 'employees' => $employees
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar formulario borrar seguimientos , contacte con el administrador');
        }
    }

    public function export(Request $request)
    {
        try {
            $params = $request->all();


            $fields = [];
            $nullfields = [];
            $notNullfields = [];
            $whereDates = [];
            $orderByField = 'id';

            if (!empty($params['centre'])) {
                if ($params['centre'] == 'SIN SELECCION') {
                    $params['centre'] = null;
                } else {
                    $fields['centre_employee'] = $params['centre'];
                }
            }
            if (!empty($params['service'])) {
                if ($params['service'] == 'SIN SELECCION') {
                    $params['service'] = null;
                } else {
                    $fields['service'] = $params['service'];
                }
            }
            if (!empty($params['employee'])) {
                if ($params['employee'] == 'SIN SELECCION') {
                    $params['employee'] = null;
                } else {
                    $fields['employee'] = $params['employee'];
                }
            }
            if (!empty($params['trackingState'])) {
                // $fieldNameDate = $params['trackingState'] . '_date';
                switch ($params['trackingState']) {
                    case 'pending':
                        $fields['state'] = 'Pendiente';
                        break;
                    case 'apointment':
                        $fields['state'] = 'Citado';
                        break;
                    case 'service':
                        $fields['state'] = 'Realizado';
                        break;
                    case 'invoiced':
                        $fields['state'] = 'Facturado';
                        break;
                    case 'validation':
                        $fields['state'] = 'Validado';
                        break;
                    case 'paid':
                        $fields['state'] = 'Pagado';
                        break;
                    case 'cancellation':
                        $fields['state'] = 'Cancelado';
                        break;
                    default:
                        // $fields['state'] = '';
                        break;
                }

                // $orderByField = $fieldNameDate;

                if ($params['trackingState'] != 'cancellation') {
                    $nullfields[] = 'cancellation_date';
                }

                if ($params['trackingState'] == 'cancellation') {
                    $notNullfields[] = 'cancellation_date';
                }
                if ($params['trackingState'] == 'SIN SELECCION') {
                    $params['trackingState'] = null;
                }
            }

            if (!empty($params['patient_name'])) {
                if ($params['patient_name'] == 'SIN SELECCION') {
                    $params['patient_name'] = null;
                } else {
                    $fields['patient_name'] = $params['patient_name'];
                }
            }
            $exportData[$params['trackingState']] = DB::table('export_tracking')
                ->where($fields)
                ->whereNull($nullfields)
                ->whereNotNull($notNullfields)
                ->whereBetween('state_date', [$params['date_from'], $params['date_to']])
                // ->orWhereBetween('service_date', [$params['date_from'], $params['date_to']])
                // ->orWhereBetween('invoiced_date', [$params['date_from'], $params['date_to']])
                // ->orWhereBetween('validation_date', [$params['date_from'], $params['date_to']])
                // ->orWhereBetween('paid_date', [$params['date_from'], $params['date_to']])
                // ->orWhereBetween('cancellation_date', [$params['date_from'], $params['date_to']])
                // ->whereBetween(array_keys($whereDates)[0], array_values($whereDates)[0])
                // ->orderBy($orderByField)
                ->get();

            $filters = [
                'centre'        => isset($params['centre'])       ?  $params['centre']       : 'TODOS',
                'service'       => isset($params['service'])      ?  $params['service']      : 'TODOS',
                'trackingState' => isset($params['trackingState'])      ?  $params['trackingState']      : 'TODOS',
                'employee'      => isset($params['employee'])     ?  $params['employee']     : 'TODOS',
                'patient_name'  => isset($params['patient_name']) ?  $params['patient_name'] : 'TODOS',
                'date_from'     => isset($params['date_from'])    ?  date('d/m/Y', strtotime($params['date_from'])) : 'TODOS',
                'date_to'       => isset($params['date_to'])      ?  date('d/m/Y', strtotime($params['date_to']))   : 'TODOS'
            ];
            ob_end_clean();
            ob_start();
            return  Excel::download((new TrackingsExport($exportData, $filters)), 'tracking.xls');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al exportar seguimientos , contacte con el administrador');
        }
    }

    public function exportForm()
    {
        try {
            $centres = Centre::getCentresActive();
            $services = Service::orderBy('name')->get();
            $patients = Tracking::getPatients();
            $employees = Employee::getEmployeesActive();

            return view('tracking.export', [
                'title'      => 'Exportar recomendaciones', 'centres'   => $centres, 'services'  => $services, 'patients'  => $patients, 'employees' => $employees
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar formulario exportar seguimientos , contacte con el administrador');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $params = $request->all();
            $id =  $params['id'];

            $tracking = Tracking::find($id);
            $fields['cancellation_date'] = date("Y-m-d H:i:s");
            $fields['state_date'] = date("Y-m-d H:i:s"); //actualizamos fecha de cambio de estado
            $fields['state'] = 'Cancelado'; //actualizamos estado
            $fields['cancellation_user_id'] = session()->get('user')->id;
            $fields['cancellation_reason'] = $params['reason'];

            /** Validar que las fechas, son las que están dentro del corte actual */
            $validateFechas = Tracking::checkDate($tracking['validation_date']);
            if ($validateFechas['result'] === false) {
                return response()->json([
                    'success' => false, 'url'    => '/tracking/deleteForm', 'mensaje' => 'La fecha de cancelación no puede ser fuera del corte actual'
                ], 400);
            }

            $tracking->update($fields);
            return response()->json([
                'success' => true, 'url'    => '/tracking/deleteForm', 'mensaje' => 'Recomendacion eliminada correctamente'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al borrar seguimiento, contacte con el administrador');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function searchDelete(Request $request)
    {

        $endPeriod = Carbon::today(); // current date
        $initPeriod = Carbon::today()->subMonths(6); // one year before the current date

        try {
            $this->user = session()->get('user');
            $centreId = $this->user->centre_id;

            $query = Tracking::getTrackings();

            $tracking = $query
            ->where(function ($q) use ($initPeriod, $endPeriod, $centreId) {
                if ($this->user->centre_id != null) {
                    $q->where('centres.id', '=', $centreId);
                }

                $q -> where(function($q2) use ($initPeriod, $endPeriod) {
                 $q2
                    ->whereNull('trackings.cancellation_date')
                    ->whereBetween('trackings.state_date', [$initPeriod, $endPeriod])
                    ->orWhereBetween('trackings.started_date', [$initPeriod, $endPeriod])
                    ->orWhereBetween('trackings.validation_date', [$initPeriod, $endPeriod])
                    ->orderBy('trackings.validation_date', 'desc');
                });
                   
            });

            return DataTables::of($tracking)
                ->addIndexColumn()
                ->addColumn('action', function ($track) {
                    $btn = '';
                    // $fnCall = 'destroy(\'' . $track->id . '\')';
                    $btn .= '<a onclick="confirmRequest(0,' . $track->id . ')" class="btn btn-red-icot a-btn-slide-text" "><span class="material-icons">
                    delete
                    </span> Borrar</a>';
                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('centres.name', 'LIKE', "%$search%")
                                ->orWhere('employees.name', 'LIKE', "%$search%")
                                ->orWhere('services.name', 'LIKE', "%$search%")
                                ->orWhere('patient_name', 'LIKE', "%$search%")
                                ->orWhere('hc', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al buscar seguimientos a borrar, contacte con el administrador');
        }
    }

    public function refreshServices(Request $request, $centre_id)
    {
        $services = Service::getServicesActive($centre_id, true);

        return json_encode(['data' => $services]);
    }

    public function refreshDiscount(Request $request, $service_id, $centre_id)
    {
        $discounts = Discount::getDiscountsByService($service_id, $centre_id);

        return json_encode(['data' => $discounts]);
    }


    /** Function to validate correct service with center allowed */
    function checkServiceCentre($params, $state)
    {
        $error = false;
        $serviceAvailable = ServicePrice::where([
            'service_id' => $params['service_id'], 'centre_id'  => $params['centre_id']
        ])->get();

        if (empty($serviceAvailable->toArray())) {
            \Log::debug('Error, intento de guardar los siguientes datos.');
            \Log::debug('$params[employee_id] = ' . $params['employee_id']);
            \Log::debug('$params[hc] = ' . $params['hc']);
            \Log::debug('$params[patient_name] = ' . $params['patient_name']);
            \Log::debug('$params[centre_id] = ' . $params['centre_id']);
            \Log::debug('$params[centre_employee_id] = ' . $params['centre_employee_id']);
            \Log::debug('$params[service_id] = ' . $params['service_id']);
            \Log::debug('$params[tracking_date] = ' . $params['tracking_date']);
            $error = true;
        }
        return $error;
    }


    /**
     * No se está usando: migración A3Innuva
     */
    public function getFinalValidationData($request)
    {
        $params = $request->all();
        $centres = Centre::getCentresActive();
        $targetService = new TargetService();

        $year  = substr($params['monthYear'], strpos($params['monthYear'], '/') + 1);
        $month = substr($params['monthYear'], 0, strpos($params['monthYear'], '/'));
        $targetDefined = $targetService->getTarget($centres, $month, $year);
        $detailedSales = [];

        $exportData = $targetService->getExportTarget($params);
        $target = $targetService->normalizeData($exportData);
        foreach ($centres as $centre) {

            $centreData = $centres->filter(function ($centreFind) use ($centre) {
                if ($centre->name == $centreFind->name) {
                    return $centreFind;
                }
            });
            if (isset($target[$centre->name])) {
                //$vcTotal =  $targetService->getVC($centreData, $target);
                $totalCentre = $targetService->getSummarySales([$centre->name => $target[$centre->name]], $targetDefined, $params['monthYear'], $centreData, $target);
                $trackingPaid = null;
                foreach ($totalCentre[$centre->name]['details'] as $eId => $detailSale) {
                    if (!empty($detailSale['dni'])) {
                        $a3empleado = A3Empleado::where('NIF', $detailSale['dni'])->whereNotNull('Nombre_Completo')->first();
                        if (!empty($a3empleado)) {
                            $detailSale['a3_nombre'] = $a3empleado->toArray()['Nombre_Completo'];
                        }
                    }
                    if ((!in_array($eId, array_keys($detailedSales)))) {
                        $detailedSales[$eId] = $detailSale;
                        $detailedSales[$eId]['name'] = isset($detailSale['a3_nombre']) ? $detailSale['a3_nombre'] : $detailSale['name'];
                        $detailedSales[$eId]['employee_id'] = $eId;
                    } else {
                        $detailSale['tracking_ids'] = isset($detailSale['tracking_ids']) ? $detailSale['tracking_ids'] : [];
                        $detailedSales[$eId]['tracking_ids'] = isset($detailedSales[$eId]['tracking_ids']) ? $detailedSales[$eId]['tracking_ids'] : [];

                        $detailedSales[$eId]['tracking_ids'] = array_merge($detailSale['tracking_ids'], $detailedSales[$eId]['tracking_ids']);
                        $detailedSales[$eId]['total_incentive'] += $detailSale['total_incentive'];
                        $detailedSales[$eId]['total_super_incentive'] += $detailSale['total_super_incentive'];
                        $detailedSales[$eId]['total_income'] += $detailSale['total_income'];
                    }
                    $detailedSales[$eId]['paid_date'] = null;
                    if (!empty($detailSale['tracking_ids'])) {
                        $trackingsPaid = Tracking::whereIn('id', $detailSale['tracking_ids'])
                            ->where('paid_done', 1)->get();

                        //Accion poner boton o poner texto de Pagado con fecha
                        if (count($detailSale['tracking_ids']) > 0  && count($trackingsPaid->toArray())  > 0) {
                            $trackingPaid = $trackingsPaid->toArray()[0];
                            $detailedSales[$eId]['paid_date'] =  date('d/m/Y', strtotime($trackingPaid['paid_date']));
                        }
                    }
                    $detailedSales[$eId]['month_year'] = $params['monthYear'];
                }
            }
        }
        $finalDetailedSales = [];
        foreach ($detailedSales as $eId => $detailedSale) {
            if ($detailedSale['total_income'] == 0) {
                continue;
            }
            $auxDetailSale = $detailedSale;
            if (isset($params['monthYear']) && !empty($params['monthYear'])) {
                $trackingBonus = TrackingBonus::select('id', 'paid_date')
                    ->where([
                        'employee_id' => $detailedSale['employee_id'], 'month_year' => $params['monthYear']
                    ])
                    ->first();
                if (!empty($trackingBonus)) {
                    $auxDetailSale['paid_date'] = date('d/m/Y', strtotime($trackingBonus->paid_date));
                }
            }

            $finalDetailedSales[$detailedSale['employee_id']] = $auxDetailSale;
        }
        $keys = array_column($finalDetailedSales, 'paid_date');
        array_multisort($keys, SORT_ASC, $finalDetailedSales);

        $finalOrderData = [];

        foreach ($finalDetailedSales as $finalDetailSale) {
            $finalOrderData[$finalDetailSale['employee_id']] = $finalDetailSale;
        }


        return $finalOrderData;
    }
    /**
     * Metodo que calcula - según mes  ( incentivos a pagar)
     * 
     */
    public function calculateValidationRRHH(Request $request)
    {
        try {
            ValidationRrhh::truncate();
            \Session::forget('validationRRHH');
            $this->finalDetailedSales = $this->getFinalValidationData($request);

            $validation = $this->finalDetailedSales;
            \Session::push('validationRRHH', $validation);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Ha ocurrido un error al calcular incentivos a pagar, contacte con el administrador');
        }
    }


    /**
     * Metodo que lista resultados de empleados a pagar, según datos calculados
     * 
     * 
     */
    public function indexFinalValidation(Request $request)
    {
        try {
            //ACCESO SOLO PARA ADMINISTRADORES
            $this->user = session()->get('user');
            if (($this->user->rol_id != 1)) {
                return redirect(RouteServiceProvider::HOME)->with('error', 'Zona restringida');
            }

            if ($request->ajax()) {
                $normalizedSales = ValidationRrhh::all();
                //$arrayNormalized = $normalizedSales->get()->toArray(); 
                if (count($normalizedSales->toArray()) == 0) {

                    $this->finalDetailedSales = \Session::get('validationRRHH');
                    if (!empty($this->finalDetailedSales)) {
                        $this->finalDetailedSales = $this->finalDetailedSales[0];
                        foreach ($this->finalDetailedSales as $k => $finalDetailSale) {
                            $d = substr($finalDetailSale['paid_date'], 0, 2);
                            $m = substr($finalDetailSale['paid_date'], 3, 2);
                            $y = substr($finalDetailSale['paid_date'], 6, 4);
                            $validation = new ValidationRrhh;
                            $validation->cod_business               = $finalDetailSale['cod_business'];
                            $validation->cod_employee               = $finalDetailSale['cod_employee'];
                            $validation->dni                        = $finalDetailSale['dni'];
                            $validation->centre                     = $finalDetailSale['centre'];
                            $validation->name                       = $finalDetailSale['name'];
                            $validation->cancellation_date          =  !empty($finalDetailSale['cancellation_date']) ? $finalDetailSale['cancellation_date'] : null;
                            $validation->paid_date                  =  !empty($finalDetailSale['paid_date'])         ? date('Y-m-d', strtotime($y . '-' . $m . '-' . $d)) : null;
                            $validation->total_income               = $finalDetailSale['total_income'];
                            $validation->employee_id                = $finalDetailSale['employee_id'];

                            $trackingIds = null;
                            if (isset($finalDetailSale['tracking_ids'])) {
                                if (is_array($finalDetailSale['tracking_ids'])) {
                                    $trackingIds = trim(implode('-', $finalDetailSale['tracking_ids']));
                                } else {
                                    $trackingIds = $finalDetailSale['tracking_ids'];
                                }
                            }

                            $validation->tracking_ids               = $trackingIds;
                            $validation->is_supervisor              = $finalDetailSale['is_supervisor'];
                            $validation->total_super_incentive      = $finalDetailSale['total_super_incentive'];
                            $validation->month_year                 = $finalDetailSale['month_year'];

                            $validation->save();
                        }
                    }
                }

                $conditions = [];
                if (isset($request->codbusiness)) {
                    $conditions['cod_business'] = $request->codbusiness;
                }
                if (isset($request->monthYear)) {
                    $conditions['month_year']   = $request->monthYear;
                    // $request->monthYear;
                }
                $query = ValidationRrhh::select(
                    'id',
                    'cod_business',
                    'cod_employee',
                    'dni',
                    'centre',
                    'name',
                    'cancellation_date',
                    'paid_date',
                    'total_income',
                    'employee_id',
                    'employee_id',
                    'tracking_ids',
                    'is_supervisor',
                    'total_super_incentive',
                    'month_year'

                );

                $normalizedSales = $query
                    ->where($conditions);
                // ->orderBy('cod_business')
                // ->orderBy('cod_employee');

                return DataTables::of($normalizedSales)
                    ->addIndexColumn()
                    ->addColumn('action', function ($detailedSale) use ($request) {
                        $btn = '<div class="row col-md-12">';
                        $trackingIds = isset($detailedSale->tracking_ids) ? $detailedSale->tracking_ids : '';

                        if (!empty($detailedSale['paid_date'])) {
                            $btn .= '<div class="col-md-6" >';
                            $formatDate = substr($detailedSale['paid_date'], 8, 2) . '/' . substr($detailedSale['paid_date'], 5, 2) . '/' . substr($detailedSale['paid_date'], 0, 4);
                            $btn .= $formatDate;
                            $btn .= '</div>';

                            //$trackingDate = date('Y-m-d');
                            //$trackingIds = isset($detailedSale->tracking_ids) ? $detailedSale->tracking_ids : '';

                            // $btn .= '<div class="col-md-7" >';
                            // $btn .= '<input style="resize:horizontal; width: 120px;" type="date" id="tracking_date_'. $detailedSale->employee_id . '" name="tracking_date" max="3000-12-31" 
                            // min="1000-01-01" value="' . $trackingDate . '" class="form-control"></input>'; 
                            // $btn .= '</div>';
                            $btn .= '<div class="col-md-2" >';
                            $fnCall = 'updateValidation(' . $detailedSale->employee_id . ', \'' . $trackingIds . '\', ' . $detailedSale->total_super_incentive . ',' . $detailedSale->is_supervisor . ' , 1)';
                            $btn .= '<a onclick="' . $fnCall . '" class="btn btn-red-icot a-btn-slide-text btn-sm">NO PAGAR</a>';
                            $btn .= '</div>';
                            $btn .= '</div>';
                        } else {
                            //$btn .= '<span style="color:red">PENDIENTE VALIDAR</span>';
                            $trackingDate = date('Y-m-d');
                            $btn .= '<div class="col-md-6" >';
                            $btn .= '<input style="resize:horizontal; width: 120px;" type="date" id="tracking_date_' . $detailedSale->employee_id . '" name="tracking_date" max="3000-12-31" 
                            min="1000-01-01" value="' . $trackingDate . '" class="form-control"></input>';
                            $btn .= '</div>';
                            $btn .= '<div class="col-md-2" >';
                            $fnCall = 'updateValidation(' . $detailedSale->employee_id . ', \'' . $trackingIds . '\', ' . $detailedSale->total_super_incentive . ',' . $detailedSale->is_supervisor . ' , 0)';
                            $btn .= '<a onclick="' . $fnCall . '" class="btn btn-success a-btn-slide-text btn-sm">Pagar</a>';
                            $btn .= '</div>';
                            $btn .= '</div>';
                        }

                        if ($detailedSale->is_supervisor == 1) { //BONUS SUPERVISOR
                            $btn .= '<div class="row col-md-12"><span style="color:red"> ' . $detailedSale->total_super_incentive . '€  BONUS SUPERVISOR</span></div>';
                        }
                        return $btn;
                    })
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhere('dni', 'LIKE', "%$search%")
                                    ->orWhere('centre', 'LIKE', "%$search%")
                                    ->orWhere('name', 'LIKE', "%$search%")
                                    ->orWhere('cod_business', 'LIKE', "%$search%")
                                    ->orWhere('cod_employee', 'LIKE', "%$search%");
                            });
                        }
                    })

                    ->rawColumns(['action'])
                    ->make(true);
            }
            $centres = Centre::getCentresActive();
            $a3business = A3Centre::select('code_business', 'name_business')->distinct()->get();
            return view('tracking.indexvalidation', [
                'title'            => 'Validar RRHH', 'mensaje'        => '', 'centres'        => $centres, 'a3business'     => $a3business
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al buscar seguimientos , contacte con el administrador');
        }
    }

    public function checkDate($trackDate, $status)
    {

        $validateData = Tracking::checkDate($trackDate);
        $response = [
            'success' => true,
            'result'    => 'ok',
            'message' => '',
        ];
        if ($validateData['result'] === false) {
            $response['success'] = false;
            $response['message'] = 'Error de fecha';
            $response['result']  = 'error';
        }
        return response()->json($response);
    }

    public function exportFinalValidation(Request $request)
    {
        try {

            if ($request->ajax()) {
                $finalDetailedSales = [];
                $finalDetailedSales = ValidationRrhh::where(['month_year' => $request->monthYear])
                    ->orderBy('cod_business')->get();

                $a3Centres = A3Centre::select(['code_business', 'name_business'])
                    ->distinct()
                    ->get();
                $codBusiness = [];
                foreach ($a3Centres as $centre) {
                    $codBusiness[$centre->code_business] = $centre->name_business;
                }
                $exportSales = [];
                foreach ($finalDetailedSales as $finalDetailedSale) {
                    $finalDetailedSale = $finalDetailedSale->toArray();
                    $nameBusiness = $finalDetailedSale['cod_business'] . '-' . $codBusiness[$finalDetailedSale['cod_business']];

                    if (
                        $finalDetailedSale['paid_date'] == date('Y-m-d')
                        || $finalDetailedSale['paid_date'] == date('d/m/Y')
                    ) {
                        $exportSales[$nameBusiness][] = $finalDetailedSale;
                    }
                }

                foreach ($exportSales as $cb => &$eSales) {
                    $keys = array_column($eSales, 'cod_employee');
                    array_multisort($keys, SORT_ASC, $eSales);
                }

                ob_end_clean();
                ob_start();
                return (new TrackingsValidateExport(collect($exportSales), array_keys($exportSales)))->download('export_incentives.xls');
            }

            $this->calculateValidationRRHH($request);

            //$this->exportFinalValidation($request);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Ha ocurrido un error al validar, contacte con el administrador');
        }
    }

    /**
     * 
     * Metodo que se encarga de validación multiple 'PAGAR TODOS'
     * @param 
     * $request (monthYear, cod_business, business, trackingIds)
     * 
     * 
     */
    public function validateTrackings(Request $request)
    {
        try {
            $user = session()->get('user');
            $data = $request->all();
            $params = [
                'paid_date'      => date('Y-m-d'), 'paid_user_id' => $user->id, 'paid_done'    => true, 'state'        => env('STATE_PAID'), 'state_date'   => date('Y-m-d')
            ];

            if (isset($request->trackingIds)) {
                $tIds = explode('-', $request->trackingIds);
                $this->updateTrackingIds($tIds, $params);
            } else {
                if (isset($request->cod_business)) {
                    $validations = ValidationRrhh::where([
                        'month_year' => $request->monthYear,
                        'cod_business' => $request->cod_business
                    ])->whereNull('paid_date')
                        ->get();
                } else {
                    $validations = ValidationRrhh::where([
                        'month_year' => $request->monthYear,
                    ])->whereNull('paid_date')
                        ->get();
                }
                if (empty($validations)) {
                    return response()->json([
                        'success' => false, 'url'    => '/tracking/indexvalidation', 'mensaje' => 'Vuelva a calcular, no hay datos'
                    ], 400);
                }
                /** Contemplar casos de baja, antes de fecha fin de corte */
                foreach ($validations as $val) {
                    $tIds = explode('-', $val->tracking_ids);
                    $this->updateTrackingIds($tIds, $params);

                    if ($val->is_supervisor == 1) {
                        $tbonus = TrackingBonus::where([
                            'month_year' => $request->monthYear, 'employee_id' => $val->employee_id
                        ])->get();
                        if (empty($tbonus->toArray())) {
                            $params['employee_id'] = $val->employee_id;
                            $params['total_income'] = $val->total_income;
                            $params['month_year']   = $request->monthYear;
                            TrackingBonus::create($params);
                            unset($params['employee_id']);
                        }
                    }
                    $validateEmployee = ValidationRrhh::where('employee_id', '=', $val->employee_id);
                    $validateEmployee->update(['paid_date' => date('Y-m-d')]);
                }
            }
            //$this->calculateValidationRRHH($request);
            //$this->exportFinalValidation($request);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Ha ocurrido un error al validar, contacte con el administrador');
        }
    }
    /**
     * 
     * Metodo que se encarga de validación multiple 'DESHACER PAGAR TODOS'
     * @param 
     * $request (monthYear, cod_business, business, trackingIds)
     * 
     * 
     */
    public function unvalidateTrackings(Request $request)
    {
        try {
            $user = session()->get('user');
            $data = $request->all();
            $params = [
                'paid_date'      => null, 'paid_user_id' => $user->id, 'paid_done'    => false, 'state'        => env('STATE_VALIDATE'), 'state_date'   => date('Y-m-d')
            ];

            if (isset($request->trackingIds)) {
                $tIds = explode('-', $request->trackingIds);
                $this->updateTrackingIds($tIds, $params);
            } else {
                if (isset($request->cod_business)) {
                    $validations = ValidationRrhh::where([
                        'month_year' => $request->monthYear,
                        'cod_business' => $request->cod_business
                    ])->whereNotNull('paid_date')
                        ->get();
                } else {
                    $validations = ValidationRrhh::where([
                        'month_year' => $request->monthYear,
                    ])->whereNotNull('paid_date')
                        ->get();
                }
                if (empty($validations)) {
                    return response()->json([
                        'success' => false, 'url'    => '/tracking/indexvalidation', 'mensaje' => 'Vuelva a calcular, no hay datos'
                    ], 400);
                }
                /** Contemplar casos de baja, antes de fecha fin de corte */
                foreach ($validations as $val) {
                    $tIds = explode('-', $val->tracking_ids);
                    $this->updateTrackingIds($tIds, $params);

                    if ($val->is_supervisor == 1) {
                        $tbonus = TrackingBonus::where([
                            'month_year' => $request->monthYear, 'employee_id' => $val->employee_id
                        ])->get();
                        if (empty($tbonus->toArray())) {
                            $params['employee_id'] = $val->employee_id;
                            $params['total_income'] = $val->total_income;
                            $params['month_year']   = $request->monthYear;
                            TrackingBonus::create($params);
                            unset($params['employee_id']);
                        }
                    }
                    $validateEmployee = ValidationRrhh::where('employee_id', '=', $val->employee_id);
                    $validateEmployee->update(['paid_date' => null]);
                }
            }
            //$this->calculateValidationRRHH($request);
            //$this->exportFinalValidation($request);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Ha ocurrido un error al validar, contacte con el administrador');
        }
    }


    private function updateTrackingIds($trackingIds, $params)
    {

        $validateFechas = null;
        foreach ($trackingIds as $tId) {
            if (!empty($tId)) {
                /** Validar que las fechas, son las que están dentro del corte actual */
                $validateFechas = Tracking::checkDate(date('Y-m-d'));
                if ($validateFechas['result'] === false) {
                    session()->flash('error', $validateFechas['message']);
                    return response()->json([
                        'success' => false, 'url'    => '/tracking/indexvalidation', 'mensaje' => $validateFechas['message']
                    ], 400);
                }
                $tracking = Tracking::find($tId);
                $tracking->update($params);
            }
        }
    }

    public function requestChange(Request $request)
    {
        $centres = Centre::getCentresActive();
        $employees = Employee::getEmployeesActive();


        return view('tracking.request_change_centre', [
            'title'     => 'Solicitudes de cambio',
            'centres'   => $centres,
            'employees' => $employees
        ]);
    }

    public function saveRequest(Request $request)
    {
        try {
            $user = session()->get('user');
            $request->validate([
                'start_date'            => 'required',
                'end_date'              => 'required',
                'centre_origin_id'      => 'required',
                'centre_destination_id' => 'required',
                'employee_id'           => 'required'
            ]);

            $params = [];
            $params = $request->all();
            $params['created_user_id'] = $user->id;

            $validateData = Tracking::checkRequestChangeDate($params['start_date'], $params['end_date']);
            if ($validateData['result'] === false) {
                return back()->with('error', $validateData['message']);
            }

            $request_id = RequestChange::create($params)->id;

            return redirect()->action('TrackingController@requestChange')

                ->with('success', 'Solicitud creada correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Formulario incompleto, faltan campos requeridos');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar seguimiento, contacte con el administrador');
        }
    }

    public function getRequestChanges()
    {
        try {
            $whereFields = "";
            $query =  DB::table('request_changes')
                ->select(
                    'request_changes.id',
                    'request_changes.start_date',
                    'request_changes.end_date',
                    'request_changes.observations',
                    'request_changes.validated',
                    'co.name as centre_origin',
                    'cd.name as centre_destination',
                    'employees.name as employee'
                )
                ->join('employees', 'employees.id', '=', 'employee_id')
                ->join('centres as co', 'co.id', '=', 'centre_origin_id')
                ->join('centres as cd', 'cd.id', '=', 'centre_destination_id');

            $user = session()->get('user');
            $centrUserId = $user->centre_id;
            if (!empty($centrUserId)) {
                $whereFields .=  " request_changes.centre_origin_id = " . $centrUserId;
                $query = $query
                    ->whereRaw($whereFields);
            }
            $requests = $query->get();

            return DataTables::of($requests)
                ->addColumn('action', function ($request) {
                    // $btn  = '<div class="row col-md-12">';
                    $btn = '';
                    if ($request->validated == 0) {
                        $btn .= '<a onClick="validateRequest(1,' . $request->id . ')" class="btn btn-success a-btn-slide-text btn-sm center" > <span class="material-icons mr-1">check</span>Validar</a>';
                        $btn .= '<a onClick="validateRequest(-1,' . $request->id . ')" class="btn btn-red-icot a-btn-slide-text btn-sm center" > <span class="material-icons mr-1">delete</span>Borrar</a>';
                    } else {
                        $btn .= '<a onClick="validateRequest(0,' . $request->id . ')" class="btn btn-red-icot a-btn-slide-text btn-sm"> <span class="material-icons mr-1">close</span>Invalidar</a>';
                    }
                    // $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar seguimiento, contacte con el administrador');
        }
    }

    //Se confirma el borrado o la actualización del registro
    public function confirmRequest(Request $request)
    {
        try {
            $params = [];
            $params = $request->all();

            $requestChange =  RequestChange::where(['id' => $params['id']]);
            $user = session()->get('user');

            switch ($params['state']) {
                case '-1':
                    $requestChange->delete();
                    break;
                case '0':
                case '1':
                    $requestChange->update([
                        'validated'         => $params['state'],
                        'validate_user_id'  => $user->id
                    ]);
                    break;
            }

            return json_encode(['data' =>  ['id'  => $params['id']]]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar seguimiento, contacte con el administrador');
        }
    }
}
