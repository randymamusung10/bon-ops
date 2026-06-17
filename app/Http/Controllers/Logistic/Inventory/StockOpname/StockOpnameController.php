<?php

namespace App\Http\Controllers\Logistic\Inventory\StockOpname;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.opname.index');
    }
}
