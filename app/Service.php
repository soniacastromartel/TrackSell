<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Service extends Model
{
    //
    protected $fillable = [
        'id',
        'name',
        'description',
        'url',
        'centre_id', //no existe centre_id en la tabla de services
        'category_id',
        'cancellation_date',
        'alias_img',
        'image'
    ];



        public function servicePrice()
        {
            return $this->hasMany(ServicePrice::class, 'service_id', 'id');
        }
        
        public function trackings() 
        {
            return $this->hasMany(Tracking::class,'service_id', 'id');
        }
    

        public static function getCompletedServices($dateFrom, $dateTo, $centreId = null)
    {
        $services = Service::query()
            ->select('services.name as serviceName', 'service_prices.price', 'centres.name as centreName', 'employees.name as employeeName')
            ->join('service_prices', 'services.id', '=', 'service_prices.service_id')
            ->join('trackings', 'services.id', '=', 'trackings.service_id')
            ->join('centres', 'trackings.centre_id', '=', 'centres.id')
            ->join('employees', 'trackings.employee_id', '=', 'employees.id')
            ->where('trackings.validation_done', 1)
            ->whereBetween('trackings.validation_date', [$dateFrom, $dateTo]);

        if ($centreId) {
            $services->where('centres.id', $centreId);
        }

        return $services->get();
    }

  
    
   
        //necesito la tabla de service_price  para obtenr el centre_id 
        // necesito price 
        // service_id para la relacion 
        

    //necesito la tabla de trakings para ver el empleado que vendió mi servicio 
    // fecha de validación para la filtración por fecha
    // employee_id para ver el empleado 
    // se relaciona con el service_id que esta en la tabla de servicios 


    public function scopeGetServicesActive($query, $centre_id = null, $basic = false, $orderDif = false, $groupBy= false) {
        $whereFields = "";
        $currentDate = date('Y-m-d H:i:s'); 
        $fields = ''; 
        if ($basic) {
            $fields = ['services.id'
                        ,'services.name'
                        ,'services.description'
                        ,'service_categories.name as category'
            ];
        } else {
            $fields = ['services.id'
                        ,'services.name'
                        ,'services.description'
                        ,'services.url'
                        ,'services.image'
                        ,'service_categories.image as category_image'
                        ,'service_categories.image_portrait as category_image_port'
                        ,'service_categories.name as category'
                        ,'service_categories.description as category_description'
                        ,'service_prices.price'
            ];
        }
        
        $query = Service::select($fields)
            ->distinct('services.name')
            ->join('service_categories', 'service_categories.id', '=', 'services.category_id')
            ->join('service_prices', function ($join) {
            $join->on('service_prices.service_id', '=', 'services.id');
            $join->whereNull('service_prices.cancellation_date');
        })
            ->join('centres', 'service_prices.centre_id', '=', 'centres.id')
            ->where(function($q) use ($currentDate) {
                $q->WhereNotNull('services.cancellation_date');
                $q->Where('services.cancellation_date', '>', $currentDate);
                $q->orWhereNull('services.cancellation_date');
        });

        if (!empty($centre_id)) {
            $whereFields .=  " centres.id = " . $centre_id;
            $query = $query 
                    ->whereRaw($whereFields); 
        }

        if ($orderDif) {
            $services = $query
                ->orderBy('services.name')->get();
        } else {
            $services = $query 
                ->orderBy('category')
                ->orderBy('services.name')->get();
        }

        if(!empty($category)){
            $services = $query
            -> where('service_categories.name', '=',$category)
            ->get();
        }

        if($groupBy){
            $services = $query
            ->groupBy('category')->get();
        }
        return $services;
    }

    public function scopeGetStateService($query, $name) 
    {
        $codeState = ''; 
        switch ($name) {
            case env('STATE_PENDING'):
                $codeState = 'started_date';
                break;
            case env('STATE_APOINTMENT'):
                $codeState = 'apointment_date';
                break;
            case env('STATE_SERVICE'):
                $codeState = 'service_date';
                break;
            case env('STATE_INVOICED'):
                $codeState = 'invoiced_date';
                break;
            case env('STATE_VALIDATE'):
                $codeState = 'validation_date';
                break;
            case env('STATE_PAID'):
                $codeState = 'paid_date';
                break;
                    
            default:
                $codeState = 'started_date';
                break;
        }
        return $codeState; 
    }

    /**
     * Busca un centro por el nombre o id recibido
     * 
     */
    public function scopeGetService4Id($query, $serviceId) 
    {
        return $query->where('id', $serviceId)->get();
    }
}