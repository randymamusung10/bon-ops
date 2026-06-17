<?php

namespace App\Http\Controllers\Operational\Restaurant\BaristaDisplay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaristaDisplayController extends Controller
{
    public function index()
    {
        return view('pages.operational.restaurant.barista.index');
    }
}
