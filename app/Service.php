<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Service extends Model
{
    protected $fillable = [
        'id',
        'name',
        'description',
        'url',
        'centre_id',
        'category_id',
        'cancellation_date',
        'alias_img',
        'image'
    ];

    //!RELATIONS
    public function servicePrice()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function tracking()
    {
        return $this->hasMany(Tracking::class);
    }

    //!FUNCIÓN DINÁMICA DE SERVICIOS


    public function scopeGetCountAllServices($query, $serviceId, $startDate, $endDate)
    {
        return $query
            ->join('trackings', 'services.id', '=', 'trackings.service_id')
            ->join('centres', 'trackings.centre_id', '=', 'centres.id')
            ->join('service_prices', function ($join) {
                $join->on('services.id', '=', 'service_prices.service_id')
                     ->on('trackings.centre_id', '=', 'service_prices.centre_id');
            })
            ->join('employees', 'trackings.employee_id', '=', 'employees.id')
            ->where('trackings.validation_done', 1)
            ->where('services.id', $serviceId)
            ->whereBetween('trackings.started_date', [$startDate, $endDate])
            ->whereBetween('trackings.validation_date', [$startDate, $endDate])
            ->whereNull('service_prices.cancellation_date')
            ->whereNull('trackings.cancellation_date')
            ->whereNull('centres.cancellation_date')
            ->select(
                'services.name as service_name',
                'centres.name as centre_name',
                'service_prices.price as price',
                'employees.name as employee_name',
                'employees.category as employee_category',

                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('services.name', 'centres.name', 'service_prices.price', 'employees.name')
            ->orderBy('cantidad', 'desc');  
    }
    


    public function scopeGetCountServicesByCentre($query, $centreId,$startDate,$endDate)
    {
        return $query->select(
            'centres.name as centre_name',
            'services.name as service_name',
            'service_prices.price as price',
            DB::raw('COUNT(*) as total')
        )
            ->join('trackings', 'services.id', '=', 'trackings.service_id')
            //función que faltaba para hacer el filtrado correctamente.
            ->join('service_prices', function ($join) use ($centreId) {
                $join->on('services.id', '=', 'service_prices.service_id')
                    ->where('service_prices.centre_id', '=', $centreId);
            })

            ->join('centres', 'trackings.centre_id', '=', 'centres.id')
            ->where('trackings.validation_done', 1)
            ->where('trackings.centre_id', $centreId)
            ->whereBetween('trackings.started_date', [$startDate, $endDate])
            ->whereBetween('trackings.validation_date', [$startDate, $endDate])
            ->whereNull('service_prices.cancellation_date')
            ->whereNull('trackings.cancellation_date')
            ->whereNull('centres.cancellation_date')
            ->groupBy('services.id', 'services.name', 'centres.name')
            ->orderBy('total', 'desc'); 
          
    }
    // funcion anterior que no salia el filtrado 

    // public function scopeGetCountServicesByCentre($query, $centreId)
    // {
    //     DB::enableQueryLog();
    //     return $query->select(
    //         'centres.name as centre_name', 
    //         'services.name as service_name', 
    //         'service_prices.price as price', 
    //         DB::raw('COUNT(*) as total')
    //     )
    //     ->join('trackings', 'services.id', '=', 'trackings.service_id')
    //     ->join('service_prices', 'services.id', '=', 'service_prices.service_id')
    //     ->join('centres', 'trackings.centre_id', '=', 'centres.id') 
    //     ->where('trackings.validation_done', 1)
    //     ->where('trackings.centre_id', $centreId)
    //     ->whereNull('service_prices.cancellation_date')
    //     ->whereNull('trackings.cancellation_date')
    //     ->whereNull('centres.cancellation_date') 
    //     ->groupBy('services.id', 'services.name', 'centres.name')
    //     ->orderBy('service_name', 'asc');
        
    // }

    public function scopeGetServicesActiveFilter($query)
    {
        return $query->whereNull('cancellation_date')
            ->orderBy('name')
            ->get();
    }
    
    // esta funcion me muestra servicios cancelados
    public function scopeGetServicesActive($query, $centre_id = null, $basic = false, $orderDif = false, $groupBy = false)
    {
        $whereFields = "";
        $currentDate = date('Y-m-d H:i:s');
        $fields = '';
        if ($basic) {
            $fields = [
                'services.id', 'services.name', 'services.description', 'service_categories.name as category'
            ];
        } else {
            $fields = [
                'services.id', 'services.name', 'services.description', 'services.url', 'services.image', 'service_categories.image as category_image', 'service_categories.image_portrait as category_image_port', 'service_categories.name as category', 'service_categories.description as category_description', 'service_prices.price'
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
            ->where(function ($q) use ($currentDate) {
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

        if (!empty($category)) {
            $services = $query
                ->where('service_categories.name', '=', $category)
                ->get();
        }

        if ($groupBy) {
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


    //?Busca un centro por el nombre o id recibido

    public function scopeGetService4Id($query, $serviceId)
    {
        return $query->where('id', $serviceId)->get();
    }
}
