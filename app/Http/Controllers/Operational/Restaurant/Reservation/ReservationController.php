<?php

namespace App\Http\Controllers\Operational\Restaurant\Reservation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return view('pages.operational.restaurant.reservations.index');
    }
}
