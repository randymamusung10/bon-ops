<?php

namespace App\Http\Controllers\Operational\Restaurant\KitchenDisplay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KitchenDisplayController extends Controller
{
    public function index()
    {
        return view('pages.operational.restaurant.kitchen.index');
    }
}
