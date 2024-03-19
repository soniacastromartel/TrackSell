<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCategories extends Model
{
    //
    protected $table = 'service_categories';
    protected $fillable = [
        'id',
        'name',
        'image',
        'description',
        'image_portrait',
        'cancellation_date',
        'services',
    ];
    
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    //Get Active Categories of Services
    public function scopeGetServiceCategoriesActive($query)
    {
        return $query->whereNull('cancellation_date')
                     ->orderBy('name')
                     ->get();
    }

    //Get Active Categories including their Services related
    public function scopeGetServiceCategoriesWithServices($query)
    {
        return $query
            ->with('services')
            ->withCount('services')
            ->whereNull('cancellation_date')
            ->orderBy('name')
            ->get();
    }
}
