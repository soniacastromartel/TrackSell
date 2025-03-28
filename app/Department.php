<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'id',
        'centre_id',
        'email',
        'supervisor_id',
        'name',
        'code',
        'parent_id'
    ];

    /** 
     * Relationships
     */
    
    public function centre()
    {
        return $this->belongsTo(Centre::class, 'centre_id');
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function scopeActAsCentres($query)
    {
        return $query->whereIn('id', config('centres_as_departments')); 
    }
}
