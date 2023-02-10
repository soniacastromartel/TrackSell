<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class A3Centre extends Model
{
    protected $table = 'a3_centres';

    protected $fillable = [
        'id',
        'code_business',
        'name_business',
        'code_centre',
        'centre_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Recoge todos los centros
     */
    public function scopeGetA3Companies()
    {
        $companies = DB::table('a3_centres')
            ->orderBy('code_business')->get();

        foreach ($companies->toArray() as $a3centre) {
            if (!empty($a3centre)) {
                $companyIds[] =  $a3centre->code_business;
            }
        }
        return array_values(array_unique($companyIds));
    }

    /**
     * Recoge todos los ids de los centros
     */
    public function scopeGetA3Centres($query,$companyCode)
    {
        $workplaces = $query->where('code_business',$companyCode)->get();
        
        foreach($workplaces as $wp){
            $centres[] =  $wp->code_centre;
        }
        return $centres;
    }

    public function scopeGetPDICentre($query,$companyCode, $workplaceCode)
    {

        $centreId = $query->where(['code_business' => $companyCode, 'code_centre' => $workplaceCode])
            ->first();

        return !empty($centreId) ? $centreId->centre_id : null;
    }

}
