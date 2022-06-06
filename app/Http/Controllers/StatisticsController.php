<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index(){
        if ($request->ajax()) {
            $trackings = DB::table('trackings')
            ->select('*')
            ->whereNull('cancellation_date');
            return $trackings;
        }
    }
}
