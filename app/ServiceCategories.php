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
        'cancellation_date'
    ];

    public function getCategoriesActive($query)
    {
        return $query->whereNull('cancellation_date')
            ->orderBy('name')
            ->get();
    }

    

}
