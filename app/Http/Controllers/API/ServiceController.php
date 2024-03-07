<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Service;
use App\ServiceCategories;
use App\Discount;

//use Illuminate\Support\Facades\Log;
class ServiceController extends BaseController
{


    //GET. SERVICES
    function servicesByCentre($idCentre, $orderDiff)
    {

        $orderBool = filter_var($orderDiff, FILTER_VALIDATE_BOOLEAN);

        if (empty($idCentre)) {
            return $this->sendError('Error: se necesita id de centro');
        }

        $services = Service::getServicesActive($idCentre, false, $orderBool);

        if (!empty($services->toArray()) && !$orderBool) {
            $services = $this->normalizeServices($services->toArray());
        }

        $success = $services;
        return $this->sendResponse($success, '');
    }

    // get discounts
    public function getAvailablesDiscounts($service, $centre)
    {
        $service = $service != null ? $service : -1;
        $centre = $centre != null ? $centre : -1;
        if (!empty($params)) {
            $service = $params['serviceId'];
            $centre = $params['centreId'];
        }

        if ($service != -1 && $centre != -1) {
            $discounts = Discount::getDiscountsByService($service, $centre);
            return $discounts;
        }
    }


    private function normalizeServices($services)
    {

        $serviceByCategory = [];
        foreach ($services as $key => $service) {
            if (!isset($serviceByCategory[$service['category']])) {
                $serviceByCategory[$service['category']] = [
                    'image'            => env('BASE_API_URL') . $service['category_image'], 'image_portrait'  => env('BASE_API_URL') . $service['category_image_port'], 'name'            => $service['category'], 'description'     => $service['category_description'], 'services'  => []
                ];
            }
            $serviceByCategory[$service['category']]['services'][] = [
                'id' => $service['id'], 'name'     => $service['name'], 'description' => $service['description'], 'image'       => env('BASE_API_URL') . $service['image'], 'url'         => $service['url'], 'price'       => $service['price']
            ];
        }

        $keys = array_keys($serviceByCategory);
        $categories = array_values($serviceByCategory);
        $serviceByCategory = array_combine(array_keys($keys), $categories);

        return $serviceByCategory;
    }

    function getServiceCategories()
    {
        return ServiceCategories::getCategoriesActive();
    }
}
