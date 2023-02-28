<?php

namespace App\Http\Controllers\API;

use App\Centre;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Service;

class PromotionsController extends BaseController{


    public function getPromotions()
    {
        // Se recogen las promociones activas
        $promos = DB::table('promotions')
                    ->where('active',  true)
                    ->get();
        $array = $promos->toArray();

        // Se recogen los id de los servicios en promo y el centro realizador
        $promosIds = explode(',', env('PROMOS_ID'));
        // TODO REVISAR QUE SEA EL CENTRO DONDE SE REALIZAN LAS PROMOS
        $centroPromo = Centre::getCentreByField(env('CENTRE_PROMO_ID')); //centreId

        foreach ($promosIds as $id) {
            $data =  Service::getService4Id($id); 

            // Recogemos el precio del servicio
            $priceService = DB::table('service_prices')
                ->select('price')
                ->where('service_id', $data[0]->id)
                ->first();

            // Se establece el precio del servicio
            $data[0]->price = $priceService->price;

            // Recogemos la imagen de la categoria de cada servicio en promocion
            $imgCategory = DB::table('service_categories')
                ->select('image_portrait')
                ->where('id', $data[0]->category_id)
                ->get();

            // Creacion de objeto a retornar con los datos de promocion
            $servicesRequest[] = array(
                'service'=>$data[0],
                'category'=>$imgCategory[0],
                'centre'=>$centroPromo);
        };
        $array[] = $servicesRequest;
        
        return $this->sendResponse($array, 'Ok');
    }
}