<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class JobCategory extends Model
{
        protected $table = 'category_job_category';
        protected $fillable = [
            'id',
            'category_name',
            'job_category_id',
            'cancellation_date'
        ];

      
}