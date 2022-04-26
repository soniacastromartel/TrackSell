<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Centre extends Model
{
        //
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
        public function scopeGetCentresActive()
        {

            $centres = DB::table('centres')
                ->whereNull('cancellation_date')
                ->orderBy('name')->get();
            return $centres; 
        }

        /**
         * Recoge los centros SIN HCT
         */
        public function scopeGetCentersWithoutHCT()
        {
            return DB::table('centres')
                ->whereNull('cancellation_date')  
                ->where('id', '!=', env('ID_CENTRE_HCT'))                      
                ->orderBy('name')->get(); 
        }

        /**
         * Busca un centro por el nombre o id recibido
         * 
         */
        public function scopeGetIdCentre($query, $centre) 
        {
            if (!is_numeric($centre)) {
                return $query->where('name', $centre)->get();
            } else {
                return $query->where('id', $centre)->get();
            }
        }
}
