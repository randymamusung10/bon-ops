<?php

namespace App\Http\Controllers\Logistic\Master\ProductionStation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductionStationController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.station.index');
    }
}
