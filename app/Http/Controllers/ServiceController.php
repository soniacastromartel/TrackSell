<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\Centre;
use App\Charts\CentreServicesGraph;
use App\Exports\AllDinamicServicesExport;
use App\Exports\DinamicServicesExport;
use DataTables;
use DB;
use Auth;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServicesIncentivesExport;
use App\ServicePrice;
use App\Tracking;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;

class ServiceController extends Controller
{

    public $user;
    public $centreId;

    public function __construct()
    {
        $this->title = 'Servicios';
        $this->user = session()->get('user');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        try {
            if ($request->ajax()) {
                $services  = DB::table('services')
                    ->select(DB::raw(
                        "services.id as id
                                , GROUP_CONCAT(centres.name ORDER BY centres.name SEPARATOR ',' ) as centre
                                , services.name
                                , services.description
                                , services.url
                                , services.image
                                , services.category_id
                                , services.cancellation_date
                                , service_categories.name as category"
                    ))
                    ->distinct()
                    ->leftjoin('service_categories', 'service_categories.id', '=', 'services.category_id')
                    ->leftjoin('service_prices', 'service_prices.service_id', '=', 'services.id')
                    ->leftjoin('centres', 'centres.id', '=', 'service_prices.centre_id')
                    ->whereNull('services.cancellation_date')
                    ->groupBy('services.id', 'services.name', 'services.description', 'services.url', 'services.image', 'services.category_id');


                return DataTables::of($services)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {

                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $r = $w->orWhere('services.name', 'LIKE', "%$search%")
                                    ->orWhere('centres.name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addColumn('action', function ($service) {
                        $btn = '';
                        if (empty($service->cancellation_date)) {
                            $btn = '<a href="services/edit/' . $service->id . '" class="btn btn-warning a-btn-slide-text"><span class="material-icons">
                            edit
                            </span> Editar</a>';
                            $btn .= '<a onclick="confirmRequest(0,' . $service->id . ')"class="btn btn-red-icot a-btn-slide-text"><span class="material-icons">
                            delete
                            </span> Borrar</a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.services.index', ['title' => $this->title, 'user' => session()->get('user')]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar servicios, contacte con el administrador');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $categories = DB::table('service_categories')->get();

            return view('admin.services.create', [
                'title' => $this->title,
                'categories' => $categories
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar servicio, contacte con el administrador');
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
        try {
            $request->validate([
                'name'        => 'required',
                'description' => 'required',
                'alias_img'   => 'required',
                'category'    => 'required',
                'changeImg'   => 'mimes:jpg'
            ]);
            $params = $request->all();

            $existsAlias = DB::table('services')
                ->select('alias_img')
                ->where('alias_img', '=', $params['alias_img'])->get()->toArray();

            if (!empty($existsAlias)) {
                return back()->with('error', 'El alias ya existe, por favor elegir otro');
            } else {
                $request->file('changeImg')->storeAs('public/img/services/',  $request->alias_img . ".jpg");

                $pathImg = env('STORAGE_IMGS_SERVICES');

                $service = array(
                    'name' => $params['name'],
                    'url' => $params['url'],
                    'category_id' => $params['category'],
                    'description' => $params['description'],
                    'alias_img' => $params['alias_img'],
                    'image' => $pathImg . $params['alias_img'] . '.jpg'
                );
                $service_id = Service::create($service)->id;

                $request->session()->put('success', 'Servicio creado correctamente');
                return redirect()->action('ServiceController@index')
                    ->with('success', 'Servicio creado correctamente');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Formulario incompleto, faltan campos requeridos');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al crear servicio, contacte con el administrador');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        try {
            $centresService = DB::table('service_prices')
                ->where(['service_prices.service_id' => $id])
                ->whereNull('service_prices.cancellation_date')
                ->get('service_prices.centre_id')
                ->pluck('centre_id')->toArray();


            $centresName = Centre::select("name")
                ->whereIn('id', $centresService)
                ->whereNull('cancellation_date')
                ->orderby('name')
                ->get(['name'])
                ->pluck('name')->toArray();

            $centres = '';
            foreach ($centresName  as $key => $centre) {
                $centres .= '- ' . $centre . PHP_EOL;
            }
            $service = Service::find($id);

            $categories = DB::table('service_categories')->get();
            $title   = $this->title;
            return view('admin.services.edit', compact(
                'service',
                'centres',
                'title',
                'categories'
            ));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Formulario incompleto, faltan campos requeridos');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar servicio para editar, contacte con el administrador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $service = Service::find($id);
            $params = $request->all();

            $pathImg = env('STORAGE_IMGS_SERVICES');

            if (isset($request['changeImg'])) {
                $request->validate([
                    'name'        => 'required',
                    'description' => 'required',
                    'category' => 'required',
                    'changeImg'   => 'mimes:jpg'
                ]);
                $res = getimagesize($request->file('changeImg'));
                $w = $res[0];
                $h = $res[1];
                if ($w != 600 && $h != 342) {
                    return back()->with('error', 'tamaño de imagen incorrecto');
                }

                $request->file('changeImg')->storeAs('public/img/services/',  $service->alias_img . ".jpg");
            } else {
                $request->validate([
                    'name'        => 'required',
                    'description' => 'required',
                ]);
            }

            $serviceUpdated = array(
                'name' => $params['name'], 'description' => $params['description'], 'url' => $params['url'], 'category_id' => $params['category']
            );

            if (isset($params['changeImg'])) {
                $serviceUpdated['changeImg'] = $pathImg . $params['alias_img'] . '.jpg';
            }

            $service->update($serviceUpdated);

            return redirect()->action('ServiceController@index')
                ->with('success', 'Servicio actualizado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Formulario incompleto, faltan campos requeridos');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al editar servicio, contacte con el administrador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        try {
            $params = $request->all();
            $id = $params['id'];
            $service = Service::find($id);
            $fields['cancellation_date'] = date("Y-m-d H:i:s");
            $service->update($fields);

            return response()->json([
                'success' => true,  'mensaje' => 'Servicio eliminado correctamente'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al eliminar servicio, contacte con el administrador');
        }
    }

    /**
     * Display a listing of the services with incentives
     *
     * @return \Illuminate\Http\Response
     */
    public function incentives(Request $request)
    {
        //
        try {

            if ($request->ajax()) {

                $params = $request->all();
                $services  = Service::select(
                    'services.id as id',
                    'centres.name as centre',
                    'services.name',
                    'service_prices.id as serviceprice_id',
                    'service_prices.price as price',
                    'service_prices.service_price_direct_incentive as incentive_direct',
                    'service_prices.service_price_incentive1 as incentive_obj1',
                    'service_prices.service_price_incentive2 as incentive_obj2',
                    'service_prices.service_price_super_incentive1 as bonus_obj1',
                    'service_prices.service_price_super_incentive2 as bonus_obj2',
                    'service_prices.cancellation_date'
                )
                    ->distinct()
                    ->join('service_prices', 'service_prices.service_id', '=', 'services.id')
                    ->join('centres', 'centres.id', '=', 'service_prices.centre_id')
                    ->where(function ($q) use ($params) {

                        if (!empty($params['centre'])) {
                            $q->where('centres.id', $params['centre']);
                        }

                        if (!empty($params['service'])) {
                            $q->where('services.id', $params['service']);
                        }
                    })
                    ->whereNull('service_prices.cancellation_date');

                return DataTables::of($services)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {

                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $r = $w->orWhere('services.name', 'LIKE', "%$search%")
                                    ->orWhere('centres.name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addColumn('action', function ($service) {
                        $btn = '';
                        $user = session()->get('user');
                        if (empty($service->cancellation_date) && $user->rol_id == 1) {
                            // $fnCall = 'destroyIncentive('.$service->serviceprice_id.' )';
                            $btn .= '<a onclick="confirmRequest(0,' . $service->serviceprice_id . ')"  class="btnDeleteServicePrice btn btn-red-icot a-btn-slide-text"><span class="material-icons">
                        delete
                        </span> Borrar</a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            $centres = Centre::getCentresActive();
            $services = Service::select(
                'services.id',
                'services.name'
            )
                ->distinct('services.name')
                ->orderBy('services.name')->get();
            return view('admin.services.incentives', [
                'title'      => 'Incentivos - Servicios', 'centres'  => $centres, 'services' => $services, 'user'     => session()->get('user')
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar servicios, contacte con el administrador');
        }
    }

    /**
     * Dar de baja servicio con su incentivo
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyIncentive(Request $request)
    {
        try {
            $params = $request->all();
            $servicePrice = DB::table('service_prices')->where('id', $params['serviceprice_id']);
            if (!empty($servicePrice)) {
                $servicePrice->update([
                    'cancellation_date'      => date('Y-m-d H:i:s', strtotime('-1 days')), 'user_cancellation_date' => session()->get('user')->id
                ]);


                session()->flash('success', 'Se ha dado de baja servicio incentivado');
                return response()->json([
                    'success' => true, 'url'    => null, 'mensaje' => 'Se ha dado de baja servicio incentivado'
                ], 200);
            } else {
                session()->flash('error', 'Error al dar de baja servicio incentivado');
                return response()->json([
                    'success' => false, 'url'    => null, 'mensaje' => 'Error'
                ], 400);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => 'false',
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    public function exportServicesIncentivesActives()
    {
        try {

            $exportData = DB::table('export_services')->get();
            ob_end_clean();
            ob_start();
            return  Excel::download((new ServicesIncentivesExport($exportData, [])), 'incentives_actives.xls');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Ha ocurrido un error al exportar, contacte con el administrador');
        }
    }


    /**
     * Displays the view of 'Dinámica de Servicios'
     * 
     * @return \Illuminate\Http\Response
     */

    //!FUNCIÓN PARA DINÁMICA DE SERVICIOS

    // public function calculateServices()
    // {
    //     try {
    //         $this->user = session()->get('user');
    //         $this->centreId = $this->user['centre_id'];
    //         $title = 'Dinámica de Servicios';

    //         if (isset($this->centreId) && $this->centreId != null) {
    //             $services = Service::getServicesActive($this->centreId, true, false);
    //             $centres = Centre::getCentreByField($this->centreId);
    //         } else {
    //             $services = Service::getServicesActive();
    //             $centres = Centre::getCentresActive();
    //         }
    //         $disabledService = false;

    //         return view('calculate_services', [
    //             'title'            => $title,
    //             'centres'         => $centres,
    //             'services'        => $services,
    //             'disabledService' => $disabledService,
    //             'user'      => $this->user
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 'false',
    //             'errors'  => $e->getMessage(),
    //         ], 400);
    //     }
    // }
    // /**
    //  * Function to to retrieve a list of services sold during a  period, and (optionally) filtered by center ID
    //  */

    // public function getSalesServices(Request $request)
    // {
    //     try {
    //         if (!$request->ajax()) {
    //             return;
    //         }

    //         $params = $request->all();
    //         $centreId = $params['centre_id'];

    //         $initPeriod = $params['dateFrom'];
    //         $endPeriod = $params['dateTo'];

    //         $servicesQuery = Service::select(

    //             'services.name',
    //             'service_prices.price',
    //             DB::raw('COUNT(trackings.id) as total'),
    //         )

    //             ->join('service_prices', 'services.id', '=', 'service_prices.service_id')
    //             ->join('trackings', 'services.id', '=', 'trackings.service_id')
    //             ->whereBetween('trackings.validation_date', [$initPeriod, $endPeriod])
    //             ->whereNull('trackings.cancellation_date')
    //             ->groupBy('services.id');

    //         if ($centreId) {
    //             $servicesQuery->where('trackings.centre_id', $centreId);
    //         }

    //         $services = $servicesQuery->get();

    //         return response()->json([
    //             "data" => $services
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 'false',
    //             'errors'  => $e->getMessage(),
    //         ], 400);
    //     }
    // }

    //!PRUEBA VIEW SOLO HTML 

    public function showAllServicesAndByCentre(Request $request)
    {
        $centreId = $request->input('centre_id');
        $serviceId = $request->input('service_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $services = Service::getServicesActiveFilter();
        $centres = Centre::getCentresActive();
        $selectedCentre = Centre::find($centreId);
        $selectedService = Service::find($serviceId);

        //TODO modificar los parámetros cuando selecciono servicio y centro
        if ($centreId && !$serviceId) {
            $servicesCount = Service::getCountServicesByCentre($centreId, $startDate, $endDate)
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->total;
                    return $item;
                })->sortByDesc('total');
        } elseif ($serviceId && !$centreId) {
            $servicesCount = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->cantidad;
                    return $item;
                })->sortByDesc('cantidad');
        } else {
            $servicesCount = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->cantidad;
                    return $item;
                })->sortByDesc('cantidad');
        }

        $totalServices = $servicesCount->sum('cantidad');
        $grandTotal = $servicesCount->sum('total_price_per_centre');
        $servicesCountCentre = Service::getCountServicesByCentre($centreId, $startDate, $endDate)
            ->get();
        //?datos para la grafica por cetro 
        $labelsCentre = $servicesCountCentre->pluck('service_name')->all();
        $dataCentre = $servicesCountCentre->pluck('total')->all();
        //?datos para la grafica por servicio
        $labelsService = $servicesCount->pluck('employee_name')->all();
        $dataService = $servicesCount->pluck('cantidad');
        //?datos para la grafica por todos los servicios
        $labelsServiceAll = $servicesCount->pluck('service_name')->all();
        $dataServiceAll = $servicesCount->pluck('cantidad')->all();
        //?datos para la grafica filtrado por centro y servicio 
        $labelsCentreService = $selectedService ? $selectedService->name : 'Servicio';
        $dataCentreService = [$totalServices];

        return view('calculateServices', [
            'services' => $services,
            'service_id' => $serviceId,
            'centre_id' => $centreId,
            'centres' => $centres,
            'selectedCentre' => $selectedCentre,
            'selectedService' => $selectedService,
            'servicesCountCentre' => $servicesCountCentre,
            'servicesCount' => $servicesCount,
            'totalServices' => $totalServices,
            'grandTotal' => $grandTotal,
            'labelsCentre' => $labelsCentre,
            'dataCentre' => $dataCentre,
            'labelsService' => $labelsService,
            'dataService' => $dataService,
            'labelsServiceAll' => $labelsServiceAll,
            'dataServiceAll' => $dataServiceAll,
            'labelsCentreService' => $labelsCentreService,
            'dataCentreService' => $dataCentreService,

        ]);
    }


    public function exportDinamicServices(Request $request)

    {
        try {
            $centreId = $request->input('centre_id');
            $serviceId = $request->input('service_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $servicesCount = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->cantidad;
                    return $item;
                })->sortByDesc('cantidad');
            $totalServices = $servicesCount->sum('cantidad');
            $centreId = $request->input('centre_id');
            $serviceId = $request->input('service_id');
            $selectedCentre = Centre::find($centreId);
            $selectedService = Service::find($serviceId);
            return Excel::download(new DinamicServicesExport($request, $selectedCentre, $selectedService, $totalServices), 'all-services.xls',  \Maatwebsite\Excel\Excel::XLS);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Ha ocurrido un error al exportar, contacte con el administrador');
        }
    }
}
