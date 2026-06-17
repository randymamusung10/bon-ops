<?php

namespace App\Http\Controllers\Logistic\Master\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.warehouse.index');
    }
}
