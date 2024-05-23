<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tracking;
use App\Centre;


class NotificationController extends Controller
{
    public function getNotifications()
    {
        try {

            $canceledTrackings = Tracking::getCancelledTrackings();
            return $canceledTrackings;
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error ' . $e);
        }
    }
}
