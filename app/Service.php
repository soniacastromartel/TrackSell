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

    // En el modelo Service
    public function centres()
    {
        return $this->belongsToMany(Centre::class, 'service_prices')
            ->using(ServicePrice::class)
            ->withPivot([
                'price',
                'service_price_direct_incentive',
                'service_price_incentive1',
                'service_price_incentive2',
                'service_price_super_incentive1',
                'service_price_super_incentive2',
                'cancellation_date',
                'user_cancellation_date'
            ]);
    }

    //get centre ids for services
    public static function getCentreIdsForAService($service_id)
    {
        return DB::table('service_centres')
            ->where('service_id', $service_id)
            ->pluck('centre_id'); 
    }

    //get centres for services
    public static function getCentersForAService($service_id)
    {
        $centreIds = DB::table('service_centres')
            ->where('service_id', $service_id)
            ->pluck('centre_id');
    
        return Centre::whereIn('id', $centreIds)->get(); // Retorna los objetos Centre
    }

    //!FUNCIÓN DINÁMICA DE SERVICIOS
    public function scopeGetCountAllServices($query, $serviceId = null, $centreId = null, $startDate = null, $endDate = null)
    {
        $query->join('trackings', 'services.id', '=', 'trackings.service_id')
            ->join('centres', 'trackings.centre_id', '=', 'centres.id')
            ->join('centres as centres_recommendation', 'trackings.centre_employee_id', '=', 'centres_recommendation.id')
            ->join('service_prices', function ($join) {
                $join->on('services.id', '=', 'service_prices.service_id')
                    ->on('trackings.centre_id', '=', 'service_prices.centre_id');
            })
            ->join('employees', 'trackings.employee_id', '=', 'employees.id')
            ->join('roles', 'employees.rol_id', '=', 'roles.id')
            ->join('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->where('trackings.validation_done', 1);

        // Filtrar por serviceId solo si se proporciona
        if (!is_null($serviceId)) {
            $query->where('services.id', $serviceId);
        }

        if (!is_null($centreId)) {
            $query->where('trackings.centre_id', $centreId);
        }

        // Filtrar por rango de fechas si ambos se proporcionan
        if (!is_null($startDate) && !is_null($endDate)) {
            $query->whereBetween('trackings.started_date', [$startDate, $endDate]);
        }

        $query->whereNull('service_prices.cancellation_date')
            ->whereNull('trackings.cancellation_date')
            ->whereNull('centres.cancellation_date')
            ->whereNull('employees.cancellation_date')
            ->select(
                'centres.name as centre_name',
                'centres_recommendation.name as centre_recommendation_name',
                'services.name as service_name',
                'service_prices.price as price',
                'employees.name as employee_name',
                'employees.rol_id as employee_rol_id',
                'employees.category as employee_category',
                'job_categories.name as category_name',
                'service_categories.name as category_service',
                'trackings.started_date',
                'trackings.validation_date as end_date',
                'trackings.centre_employee_id',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('COUNT(DISTINCT trackings.centre_employee_id) as centre_recommendation_count')
            );

        $query->orderBy('cantidad', 'desc');

        return $query;

    }

    public function scopeGetCountServicesByCentre($query, $centreId, $startDate, $endDate)
    {
        $query->select(
            'centres.name as centre_name',
            'services.name as service_name',
            'service_prices.price as price',
            'trackings.started_date',
            'trackings.validation_date as end_date',
            DB::raw('COUNT(trackings.id) as total')  // Cambiado para contar específicamente los seguimientos
        )
            ->join('trackings', 'services.id', '=', 'trackings.service_id', 'left')  // Cambio a left join si necesario
            ->join('centres', 'trackings.centre_id', '=', 'centres.id', 'left')
            ->join('service_prices', function ($join) use ($centreId) {
                $join->on('services.id', '=', 'service_prices.service_id')
                    ->where('service_prices.centre_id', '=', $centreId);
            }, 'left')  // Cambio a left join si se espera mostrar servicios sin precios específicos

            ->where('trackings.validation_done', 1)
            ->where('trackings.centre_id', $centreId);

        if ($startDate && $endDate) {
            $query->whereBetween('trackings.started_date', [$startDate, $endDate]);
        }

        $query->whereNull('service_prices.cancellation_date')
            ->whereNull('trackings.cancellation_date')
            ->whereNull('centres.cancellation_date')
            ->groupBy('services.id', 'services.name', 'centres.name')
            ->orderBy('total', 'desc');

        return $query;
    }

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
                'services.id',
                'services.name',
                'services.description',
                'service_categories.name as category'
            ];
        } else {
            $fields = [
                'services.id',
                'services.name',
                'services.description',
                'services.url',
                'services.image',
                'service_categories.image as category_image',
                'service_categories.image_portrait as category_image_port',
                'service_categories.name as category',
                'service_categories.description as category_description',
                'service_prices.price'
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
            $whereFields .= " centres.id = " . $centre_id;
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

    public function scopeFindServiceIdByColumn($query, $column, $value)
    {
        return $query->where($column, $value)->value('id');
    }

}