<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Role extends Model
{
    //
    protected $fillable = [
        'id',
        'name',
        'description',
        'level_id'

    ];


    public function scopeGetRolesActive(){

        $roles = DB::table('roles')
                    ->whereNull('cancellation_date')
                    ->orderBy('name')->get();
        return $roles;
    }
}
