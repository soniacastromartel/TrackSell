<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class JobCategory extends Model
{
        //
        protected $fillable = [
            'id',
            'name'
        ];
      
}