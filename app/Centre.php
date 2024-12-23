<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Centre extends Model
{
    protected $table = 'centres'; // Set the table name

    protected $fillable = [
        'id',
        'name',
        'label',
        'address',
        'phone',
        'email',
        'timetable',
        'island',
        'alias_img',
        'image',
        'cancellation_date'
    ];

    /**
     * Recoge todos los centros
     */
    public function scopeGetCentresActive($query)
    {
        return $query->whereNull('cancellation_date')
            ->orderBy('name')
            ->get();
    }

    /**
     * Recoge los centros SIN HCT
     */
    public function scopeGetCentersWithoutHCT($query)
    {
        return $query->whereNull('cancellation_date')
            ->where('id', '!=', env('ID_CENTRE_HCT'))
            ->orderBy('name')
            ->get();
    }

    /**
     * Busca un centro por el nombre o id recibido
     */
    public function scopeGetCentreByField($query, $field)
    {
        if (!is_numeric($field)) {
            return $query
            ->whereNull('cancellation_date')
            ->where('name', $field)->get();
        } else {
            return $query
            ->whereNull('cancellation_date')
            ->where('id', $field)->get();
        }
    }

    /**
     * Busca un centro por el nombre o id recibido
     */
    public function scopeGetCentreName($query, $centre)
    {
        if (!is_numeric($centre)) {
            $centre = $query
            ->whereNull('cancellation_date')
            ->where('name', $centre)->first();
        } else {
            $centre = $query
            ->whereNull('cancellation_date')
            ->where('id', $centre)->first();
        }

        return !empty($centre) ? $centre->name : null;
    }

    /**
     * Devuelve la lista de centros para un servicio determinado
     */
    public function scopeGetCentersByServiceId($query, $serviceId)
    {
        return $query->select('centres.*')
            ->join('service_prices', 'centres.id', '=', 'service_prices.centre_id')
            ->join('services', 'services.id', '=', 'service_prices.service_id')
            ->where('services.id', $serviceId)
            ->groupBy('centres.id')
            ->get();
    }

    /**
     * Find a center where the name is like the given string.
     */
    public function scopeGetCentreByNameLike($query, $name)
    {
        return $query
        ->whereNull('cancellation_date')
        ->where('name', 'like', '%' . $name . '%')->get();
    }

    /**
     * Find the ID of a center where the name is like the given string.
     */
    public function scopeGetCentreIdByNameLike($query, $name)
    {
        $centreId = $query
        ->whereNull('cancellation_date')
        ->where('name', 'like', '%'. $name.'%')->value('id');
        Log::info('centreId: ', [
            
        ]);
        return $centreId;    }

    

    /**
     * Devuelve el email de un centro por su ID
     */
    public function scopeGetEmailByCenterId($query, $centerId)
    {
        $centre = $query
        ->whereNull('cancellation_date')
        ->where('id', $centerId)->first();

        return $centre ? $centre->email : null;
    }

}
