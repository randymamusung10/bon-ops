<?php

namespace App\Http\Controllers\Logistic\Inventory\StockAdjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.adjustment.index');
    }
}
