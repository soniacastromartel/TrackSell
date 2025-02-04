<?php

namespace App\Http\Controllers;
use App\Service;
use App\Centre;
use App\ServicePrice;
use DataTables;
use DB;

use Illuminate\Http\Request;

class IncentivesController extends Controller
{
    /**
     * Display a listing of the service_prices with incentives
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        try {

            if ($request->ajax()) {
                $params = $request->all();
                $services = Service::select(
                    'services.id as id',
                    'centres.name as centre',
                    'services.name as service',
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
                            $btn .= '<a href="javascript:void(0);" class="btn-edit" 
                            data-id="' . $service->serviceprice_id . '" 
                            data-name="' . $service->service . '" 
                            data-price="' . $service->price . '" 
                            data-direct-incentive="' . $service->incentive_direct . '" 
                            data-obj1="' . $service->incentive_obj1 . '" 
                            data-obj2="' . $service->incentive_obj2 . '" 
                            data-bonus1="' . $service->bonus_obj1 . '" 
                            data-bonus2="' . $service->bonus_obj2 . '">
                            <span class="material-symbols-outlined">edit</span>
                        </a>';
                            $btn .= '<a onclick="confirmRequest(0,' . $service->serviceprice_id . ')"  class="btn-delete"><span class="material-symbols-outlined">
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
            return view('incentives', [
                'title' => 'Incentivos - Servicios',
                'centres' => $centres,
                'services' => $services,
                'user' => session()->get('user')
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar incentivos: ' . $e->getMessage());
        }
    }

    public function updateIncentive(Request $request)
    {
        $request->validate([
            'price' => 'required|numeric',
            'incentive_direct' => 'required|numeric',
            'incentive_obj1' => 'required|numeric',
            'incentive_obj2' => 'required|numeric',
            'bonus_obj1' => 'required|numeric',
            'bonus_obj2' => 'required|numeric',
        ]);

        $incentive = ServicePrice::findOrFail($request->service_id);
        $incentive->update([
            'price' => $request->input('price'),
            'incentive_direct' => $request->input('incentive_direct'),
            'incentive_obj1' => $request->input('incentive_obj1'),
            'incentive_obj2' => $request->input('incentive_obj2'),
            'bonus_obj1' => $request->input('bonus_obj1'),
            'bonus_obj2' => $request->input('bonus_obj2'),
        ]);

        return response()->json([
            'message' => 'Incentivo Actualizado Correctamente',
            'data' => $incentive
        ]);
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
                    'cancellation_date' => date('Y-m-d H:i:s', strtotime('-1 days')),
                    'user_cancellation_date' => session()->get('user')->id
                ]);

                session()->flash('success', 'Se ha dado de baja servicio incentivado');
                return response()->json([
                    'success' => true,
                    'url' => null,
                    'mensaje' => 'Se ha dado de baja servicio incentivado'
                ], 200);
            } else {
                session()->flash('error', 'Error al dar de baja servicio incentivado');
                return response()->json([
                    'success' => false,
                    'url' => null,
                    'mensaje' => 'Error'
                ], 400);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => 'false',
                'errors' => $e->getMessage(),
            ], 400);
        }
    }
}
