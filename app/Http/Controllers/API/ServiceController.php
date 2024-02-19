<?php

namespace App\Http\Controllers\API;

use App\Centre;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Service;
use App\ServiceCategories;
use App\Discount;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

//use Illuminate\Support\Facades\Log;
class ServiceController extends BaseController
{

    //Get Active Services
    function getServices()
    {
        $services = Service::getServicesActive(null, false, false, true);
        return $services;
    }
    function getServicesByCategory($category)
    {
        $services = Service::getServicesActive(null, false, false, true, $category);
        return $services;
    }

    //Get Services Filtering by Centre
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


    //Get Active Service Categories
    function getServiceCategories()
    {
        $serviceCategories = ServiceCategories::getServiceCategoriesActive();
        return $serviceCategories;
    }

    function getServiceCategoriesWithServices(){
        $serviceCategories = ServiceCategories::getServiceCategoriesWithServices();
        return $serviceCategories;
    }
    
    function getServicesWithCenters($id){
        $centers = Centre::getGetCentersByServiceId($id);
        return $centers;
    }
}
