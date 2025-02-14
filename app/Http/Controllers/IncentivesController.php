<?php

namespace App\Http\Controllers;
use App\Service;
use App\Centre;
use App\ServicePrice;
use DataTables;
use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Imports\IncentiveImport;

class IncentivesController extends Controller
{
    /**
     * Display a listing of the service_prices with incentives
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = session()->get('user');

        try {
            if ($request->ajax()) {
                $params = $request->all();

                $services = ServicePrice::select(
                    'services.id as id',
                    'services.name as service',
                    'centres.name as centre',
                    'service_prices.id as serviceprice_id',
                    'service_prices.price as price',
                    'service_prices.service_price_direct_incentive as incentive_direct',
                    'service_prices.service_price_incentive1 as incentive_obj1',
                    'service_prices.service_price_incentive2 as incentive_obj2',
                    'service_prices.service_price_super_incentive1 as bonus_obj1',
                    'service_prices.service_price_super_incentive2 as bonus_obj2',
                    'service_prices.cancellation_date'
                )
                    ->join('centres', 'centres.id', '=', 'service_prices.centre_id')
                    ->join('services', 'services.id', '=', 'service_prices.service_id')
                    ->when(!empty($params['centre']), function ($query) use ($params) {
                        $query->where('centres.id', $params['centre']);
                    })
                    ->when(!empty($params['service']), function ($query) use ($params) {
                        $query->where('services.id', $params['service']);
                    })
                    ->whereNull('service_prices.cancellation_date')
                    ->groupBy('service_prices.service_id')
                ;
                return DataTables::of($services)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhere('services.name', 'LIKE', "%$search%");
                                // ->orWhere('centres.name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addColumn('action', function ($service) use ($user) {
                        return $this->generateActionButtons($service, $user);
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            // Obtener datos para la vista
            $centres = Centre::getCentresActive();
            $services = Service::select('services.id', 'services.name')
                ->distinct()
                ->orderBy('services.name')
                ->get();

            return response()->view('incentives', [
                'title' => 'Incentivos - Servicios',
                'centres' => $centres,
                'services' => $services,
                'user' => $user
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Error de base de datos al cargar incentivos: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->to('home')->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
        }
    }

    public function generateActionButtons($service, $user)
    {
        $btn = '';
        if (empty($service->cancellation_date) && $user->rol_id == 1) {
            $btn .= '<a href="javascript:void(0);" class="btn-edit" title="Editar"
                     data-id="' . $service->serviceprice_id . '" 
                     data-name="' . $service->service . '" 
                     data-centre="' . $service->centre . '" 
                     data-price="' . $service->price . '" 
                     data-direct-incentive="' . $service->incentive_direct . '" 
                     data-obj1="' . $service->incentive_obj1 . '" 
                     data-obj2="' . $service->incentive_obj2 . '" 
                     data-bonus1="' . $service->bonus_obj1 . '" 
                     data-bonus2="' . $service->bonus_obj2 . '"
                     >
                     <span class="material-symbols-outlined">edit</span>
                 </a>';
            $btn .= '<a href="javascript:void(0);" class="btn-see" title="Ver Centros"
                     data-id="' . $service->serviceprice_id . '" 
                     data-name="' . $service->service . '" 
                     data-centre="' . $service->centre . '" 
                     data-price="' . $service->price . '" 
                     data-direct-incentive="' . $service->incentive_direct . '" 
                     data-obj1="' . $service->incentive_obj1 . '" 
                     data-obj2="' . $service->incentive_obj2 . '" 
                     data-bonus1="' . $service->bonus_obj1 . '" 
                     data-bonus2="' . $service->bonus_obj2 . '">
                     <span class="material-symbols-outlined">home_pin</span>
                 </a>';
            $btn .= '<a href="javascript:void(0);" id= "btn-repeat" class="btn-search-circle" title="Añadir a Centro"
                     data-id="' . $service->serviceprice_id . '" 
                     data-name="' . $service->service . '" 
                     data-centre="' . $service->centre . '" 
                     data-price="' . $service->price . '" 
                     data-direct-incentive="' . $service->incentive_direct . '" 
                     data-obj1="' . $service->incentive_obj1 . '" 
                     data-obj2="' . $service->incentive_obj2 . '" 
                     data-bonus1="' . $service->bonus_obj1 . '" 
                     data-bonus2="' . $service->bonus_obj2 . '">
                     <span class="material-symbols-outlined">add_home_work</span>
                 </a>';
            $btn .= '<a onclick="confirmRequest(0,' . $service->serviceprice_id . ')"  class="btn-delete" title="Eliminar">
                     <span class="material-symbols-outlined">delete</span>
                 </a>';
        }
        return $btn;
    }


    public function createIncentive(Request $request)
    {
        $params = $request->validate([
            'price' => 'required|numeric|min:0',
            'service_name' => 'required|string',
            'centre_name' => 'required|string',
            'service_price_direct_incentive' => 'required|numeric|min:0',
            'service_price_incentive1' => 'required|numeric|min:0',
            'service_price_incentive2' => 'required|numeric|min:0',
            'service_price_super_incentive1' => 'required|numeric|min:0',
            'service_price_super_incentive2' => 'required|numeric|min:0',
        ]);

        try {
            $service_id = Service::findServiceIdByColumn('name', $request->service_name);
            $centre_id = Centre::getCentreIdByNameLike($params['centre_name']);

            // Verificar si ya existe un incentivo con el mismo service_id y centre_id
            $existingIncentive = ServicePrice::where('service_id', $service_id)
                ->where('centre_id', $centre_id)
                ->first();

            if ($existingIncentive) {
                return response()->json([
                    'success' => false,
                    'message' => 'El incentivo ya existe para este servicio y centro.',
                ], 400);
            }

            // Crear un nuevo incentivo
            $data = [
                'price' => $params['price'],
                'service_price_direct_incentive' => $params['service_price_direct_incentive'],
                'service_price_incentive1' => $params['service_price_incentive1'],
                'service_price_incentive2' => $params['service_price_incentive2'],
                'service_price_super_incentive1' => $params['service_price_super_incentive1'],
                'service_price_super_incentive2' => $params['service_price_super_incentive2'],
                'service_id' => $service_id,
                'centre_id' => $centre_id,
            ];

            $incentive = ServicePrice::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Incentivo creado correctamente.',
                'data' => $incentive,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el incentivo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function updateIncentive(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|numeric', // Validamos que venga el ID
            'price' => 'required|numeric',
            'service_price_direct_incentive' => 'required|numeric',
            'service_price_incentive1' => 'required|numeric',
            'service_price_incentive2' => 'required|numeric',
            'service_price_super_incentive1' => 'required|numeric',
            'service_price_super_incentive2' => 'required|numeric',
        ]);

        try {
            $incentive = ServicePrice::findOrFail($data['service_id']);

            $incentive->update($request->only([
                'price',
                'service_price_direct_incentive',
                'service_price_incentive1',
                'service_price_incentive2',
                'service_price_super_incentive1',
                'service_price_super_incentive2',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Incentivo actualizado correctamente',
                'data' => $incentive
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar incentivo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el incentivo',
            ], 500);
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
            $servicePrice = ServicePrice::where('id', $params['serviceprice_id']);
            if (!empty($servicePrice)) {
                ServicePrice::cancelServicePrice(session()->get('user')->id);
                session()->flash('success', 'Se ha dado de baja servicio incentivado');
                return response()->json([
                    'success' => true,
                    'url' => null,
                    'message' => 'Se ha dado de baja servicio incentivado'
                ], 200);
            } else {
                session()->flash('error', 'Error al dar de baja servicio incentivado');
                return response()->json([
                    'success' => false,
                    'url' => null,
                    'message' => 'Error'
                ], 400);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => 'false',
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * handles incentives import
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function import(Request $request)
    {
        if ($request->hasFile('incentiveInputFile')) {
            try {
                $fileName = 'incentiveInputFile';
                $storageFileName = env('IMPORT_INCENTIVES_FILE');
                $filePath = base_path('storage') . '/' . $storageFileName;

                // Validate the file
                $validator = Validator::make($request->all(), [
                    $fileName => 'max:2048|mimes:xls',
                ]);

                if ($validator->fails()) {
                    Log::error("La validación del archivo ha fallado.");
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Error: el archivo supera el tamaño permitido o no tiene un formato válido de Excel.'
                    ], 400);
                }

                $request->file($fileName)->move(storage_path(), $storageFileName);

                try {
                    Excel::import(new IncentiveImport(), $filePath);
                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                    $failures = $e->failures();
                    foreach ($failures as $failure) {
                        \Log::error("Error en la fila {$failure->row()}: " . implode(', ', $failure->errors()));
                    }
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Errores de validación durante la importación.',
                        'detalles' => $failures
                    ], 400);
                } catch (\Maatwebsite\Excel\Exceptions\SheetNotFoundException $e) {
                    \Log::error("Hoja no encontrada en el archivo: " . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Error: hoja no encontrada en el archivo.'
                    ], 400);
                } catch (\Exception $e) {
                    Log::error("Error durante la importación: " . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Error durante la importación: ' . $e->getMessage()
                    ], 500);
                } finally {
                    // Eliminar el archivo después de la importación
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                return response()->json([
                    'success' => true,
                    'mensaje' => 'Importación realizada con éxito.'
                ]);
            } catch (\Exception $e) {
                Log::error("Error al procesar el archivo: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Error al procesar el archivo: ' . $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se ha proporcionado ningún archivo para importar.'
            ], 400);
        }
    }



}
