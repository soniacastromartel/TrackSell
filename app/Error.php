<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
        //
        protected $fillable = [
            'phone',
            'uuid',
            'model',
            'version',
            'fabricante',
            'error',
            'fecha'
        ];

}