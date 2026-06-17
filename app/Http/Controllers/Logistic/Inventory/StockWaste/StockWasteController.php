<?php

namespace App\Http\Controllers\Logistic\Inventory\StockWaste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockWasteController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.waste.index');
    }
}
