<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BtnController extends Controller
{
    public function returnView(){
        return view('btnDesign');
    }
}
