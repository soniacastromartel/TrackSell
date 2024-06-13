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
                            $btn = '<a href="services/edit/' . $service->id . '" class="btn-edit"><span class="material-icons">
                            edit
                            </span></a>';
                            $btn .= '<a onclick="confirmRequest(0,' . $service->id . ')"class="btn-delete"><span class="material-icons">
                            delete
                            </span></a>';
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
            $centres = Centre::getCentresActive();

            return view('admin.services.create', [
                'title' => $this->title,
                'categories' => $categories,
                'centres' => $centres
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
                'alias_img'   => 'required|unique:services,alias_img',
                'category'    => 'required',
                'changeImg'   => 'required|mimes:jpg,png,jpeg|max:2048'
            ]);
    
            $params = $request->all();
    
            if ($request->hasFile('changeImg')) {
                $image = $request->file('changeImg');
                $imageName = $request->alias_img . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/img/services', $imageName);
                $imagePath = 'img/services/' . $imageName;
            } else {
                $imagePath = 'img/default.png';
            }
    
            // Crear el servicio
            $service = Service::create([
                'name' => $params['name'],
                'url' => $params['url'],
                'category_id' => $params['category'],
                'description' => $params['description'],
                'alias_img' => $params['alias_img'],
                'image' => $imagePath,
               
            ]);
    
            // Redirigir a la vista index con un mensaje de éxito
            return redirect()->route('services.index')->with('success', 'Servicio creado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Mostrar los errores de validación para depuración
            return back()->with('error', 'Ya existe este servicio o campos requeridos incompletos');
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

                $centres = Centre::select("id", "name")
                ->whereIn('id', $centresService)
                ->whereNull('cancellation_date')
                ->orderby('name')
                ->get();
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
                            $btn .= '<a onclick="confirmRequest(0,' . $service->serviceprice_id . ')"  class="btn-delete"><span class="material-icons">
                        delete</span></a>';
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


    //!PRUEBA VIEW SOLO HTML 

    public function showAllServicesAndByCentre(Request $request)
    {
        $centreId = $request->input('centre_id');
        $serviceId = $request->input('service_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $services = Service::getServicesActiveFilter();
        $centres = Centre::getCentresActive();
        $selectedCentre = !empty($centreId) ? Centre::find($centreId) : null;
        $selectedService = !empty($serviceId) ? Service::find($serviceId) : null;
        //Todos centros
        if ($centreId && !$serviceId) {
            $servicesCount = Service::getCountServicesByCentre($centreId, $startDate, $endDate)
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->total;
                    return $item;
                })
                ->sortByDesc('total');
         //Todos servicios       
        } elseif ($serviceId && !$centreId) {
            $servicesCount = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
                ->groupBy('employees.name', 'centres.name','service_prices.price','services.name')
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->cantidad;
                    return $item;
                })->sortByDesc('cantidad');

        //Todos los centros y servicios        
        } elseif (!$serviceId && !$centreId)  {
            $servicesCount = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
                ->groupBy('services.name')
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->cantidad;
                    return $item;
                })->sortByDesc('cantidad');

             

               
             
        } else {
            //Todo un centros y  un servicio
            $servicesCount = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
                ->groupBy('employees.name', 'centres.name','service_prices.price','services.name')
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->cantidad;
                    return $item;
                })->sortByDesc('cantidad');
        }

        $servicesCountGroupService =  Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
           ->groupBy('centres.name','service_prices.price','services.name')
            ->get()
            ->map(function ($item) {
                $item->total_price_per_centre = $item->price * $item->cantidad;
                return $item;
            })->sortByDesc('cantidad');

        $totalServices = $servicesCount->sum('cantidad');
        $grandTotal = $servicesCount->sum('total_price_per_centre');
        $servicesCountCentre = Service::getCountServicesByCentre($centreId, $startDate, $endDate)
            ->get();
        $serviceByCentre = Service::getCountAllServices($serviceId, null, $startDate,$endDate)
        ->groupBy('centre_name','services.name')
        ->get()
        ->map(function ($item) {
            $item->total_price = $item->price * $item->cantidad;
            return $item;
        })->sortByDesc('cantidad');

        $serviceEmployeeCategory = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
        ->groupBy('category_name')
        ->get()
        ->map(function ($item) {
            $item->total_price_per_centre = $item->price * $item->cantidad;
            return $item;
        })->sortByDesc('cantidad');

        $serviceCategory = Service::getCountAllServices($serviceId, $centreId,$startDate, $endDate)
        ->groupBy('category_service')
        ->get()
        ->sortByDesc('cantidad');

        $serviceEmployee = Service::getCountAllServices($serviceId, $centreId, $startDate, $endDate)
        ->groupBy('employees.name')
        ->get()
        ->sortByDesc('cantidad');
       

        //?datos grafica para el total de servicios en todos los centros 
        $labelsServiceAllTotal = [$selectedService ? $selectedService->name : ''];
        $dataServiceAllTotal =  [$totalServices];
        //?datos para la grafica por cetro 
        $labelsCentre = $servicesCountCentre->pluck('service_name')->all();
        $dataCentre = $servicesCountCentre->pluck('total')->all();
        //?datos para la grafica por servicio
        $labelsService = $servicesCountGroupService->pluck('centre_name')->all();
        $dataService = $servicesCountGroupService->pluck('cantidad');
        //?datos para la grafica por todos los servicios
        $labelsServiceAll = $servicesCount->pluck('service_name')->all();
        $dataServiceAll = $servicesCount->pluck('cantidad')->all();
        $dataTotalService = [$grandTotal];
        //?datos para la grafica filtrado por centro y servicio 
        $labelsCentreService = $selectedService ? $selectedService->name : 'Servicio';
        $dataCentreService = [$totalServices];
        //?datos para la grafica ventas servicios por categoría de empleado 
        $labelsEmployeeCategory = $serviceEmployeeCategory->pluck('category_name')->all();
        $dataEmployeeCategory = $serviceEmployeeCategory->pluck('cantidad')->all();
        //?datos para la grafica ventas servicios por categoría de servicios 
        $labelsServiceCategory = $serviceCategory->pluck('category_service')->all();
        $dataServiceCategory = $serviceCategory->pluck('cantidad')->all();
        //?datos para la grafica ventas servicios por empleados
         $labelsServiceEmployee = $servicesCount->pluck('employee_name')->all();
         $dataServiceEmployee = $servicesCount->pluck('cantidad')->all();
        //? datos para la gráfica de ventas totales de empleados
        $labelsTotalEmployee = $serviceEmployee->pluck('employee_name')->all();
        $dataTotalEmployee = $serviceEmployee->pluck('cantidad')->all();


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
            'dataTotalService' => $dataTotalService,
            'labelsCentreService' => $labelsCentreService,
            'dataCentreService' => $dataCentreService,
            'servicesCountGroupService' => $servicesCountGroupService,
            'serviceEmployeeCategory' => $serviceEmployeeCategory,
            'serviceCategory' => $serviceCategory,
            'labelsEmployeeCategory' => $labelsEmployeeCategory,
            'dataEmployeeCategory' => $dataEmployeeCategory,
            'labelsServiceCategory' => $labelsServiceCategory,
            'dataServiceCategory' => $dataServiceCategory,
            'labelsServiceEmployee' => $labelsServiceEmployee,
            'dataServiceEmployee' => $dataServiceEmployee,
            'labelsServiceAllTotal' => $labelsServiceAllTotal,
            'dataServiceAllTotal' => $dataServiceAllTotal,
            'serviceByCentre' => $serviceByCentre,
            'serviceEmployee' => $serviceEmployee,
            'labelsTotalEmployee' => $labelsTotalEmployee,
            'dataTotalEmployee' => $dataTotalEmployee,
           

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
               ->groupBy('employees.name', 'centres.name','service_prices.price')
                ->get()
                ->map(function ($item) {
                    $item->total_price_per_centre = $item->price * $item->cantidad;
                    return $item;
                })->sortByDesc('cantidad');
            $totalServices = $servicesCount->sum('cantidad');
            $grandTotal = $servicesCount->sum('total_price_per_centre');
            $centreId = $request->input('centre_id');
            $serviceId = $request->input('service_id');
            $selectedCentre = Centre::find($centreId);
            $selectedService = Service::find($serviceId);
            ob_end_clean();
            ob_start();
            return Excel::download(new DinamicServicesExport($request, $selectedCentre, $selectedService, $totalServices, $grandTotal), 'services.xls');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Ha ocurrido un error al exportar, contacte con el administrador');
        }
    }
}
