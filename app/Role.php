<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Role extends Model
{

    protected $table = 'roles';
    //
    protected $fillable = [
        'id',
        'name',
        'description',
        'level_id'

    ];


    public function scopeGetRolesActive()
    {

        $roles = self::whereNull('cancellation_date')
            ->orderBy('name')->get();
        return $roles;
    }

    public function scopeGetRoleById($query, $id)
    {
        return $query->where('id', $id)->first();

    }
    
}
