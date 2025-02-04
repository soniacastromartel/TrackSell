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
        'cancellation_date',
        'parent_id' 
    ];

    /** 
     * Relationships
     */
    public function parent()
    {
        return $this->belongsTo(Centre::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Centre::class, 'parent_id');
    }

    /**
     * Recoge todos los centros activos
     */
    public function scopeGetCentresActive($query)
    {
        return $query->whereNull('cancellation_date')
            ->orderBy('name')
            ->get();
    }

    /**
     * Recoge todos los centros activos ordenados por campos preconfigurados
     */
    public function scopeGetCentresActiveByRaw($query, $rawFields)
    {
        return $query->whereNull('cancellation_date')
            ->orderByRaw($rawFields)
            ->get();
    }

    /**
     * Recoge los centros SIN HCT (Excluye HCT y sus departamentos)
     */
    public function scopeGetCentersWithoutHCT($query)
    {
        // Get all child centers of HCT
        $hct = self::where('name', 'HOSPITAL TELDE')->first();

        if ($hct) {
            $excludedIds = self::where('parent_id', $hct->id)->pluck('id')->toArray();
            $excludedIds[] = $hct->id; // Also exclude HCT itself
        } else {
            $excludedIds = [env('ID_CENTRE_HCT')];
        }

        return $query->whereNull('cancellation_date')
            ->whereNotIn('id', $excludedIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Busca un centro por el nombre o id recibido
     */
    public function scopeGetCentreByField($query, $field)
    {
        return $query->whereNull('cancellation_date')
            ->where(is_numeric($field) ? 'id' : 'name', $field)
            ->get();
    }

    /**
     * Get the name of a center by ID or Name
     */
    public function scopeGetCentreName($query, $centre)
    {
        $centre = $query->whereNull('cancellation_date')
            ->where(is_numeric($centre) ? 'id' : 'name', $centre)
            ->first();

        return $centre ? $centre->name : null;
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
        return $query->whereNull('cancellation_date')
            ->where('name', 'like', '%' . $name . '%')
            ->get();
    }

    /**
     * Find the ID of a center where the name is like the given string.
     */
    public function scopeGetCentreIdByNameLike($query, $name)
    {
        return $query->whereNull('cancellation_date')
            ->where('name', 'like', '%' . $name . '%')
            ->value('id');
    }

    /**
     * Devuelve el email de un centro por su ID
     */
    public function scopeGetEmailByCenterId($query, $centerId)
    {
        return $query->whereNull('cancellation_date')
            ->where('id', $centerId)
            ->value('email');
    }

    /**
     * Get all centres including their child departments
     */
    public function scopeGetAllCentersWithChildren($query)
    {
        return $query->with('children')->whereNull('cancellation_date')->get();
    }

    /**
     * Check if a center has child departments
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }
}
