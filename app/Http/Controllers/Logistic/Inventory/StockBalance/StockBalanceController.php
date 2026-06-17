<?php

namespace App\Http\Controllers\Logistic\Inventory\StockBalance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockBalanceController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.balance.index');
    }
}
