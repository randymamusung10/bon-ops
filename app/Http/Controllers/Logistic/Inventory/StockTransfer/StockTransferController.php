<?php

namespace App\Http\Controllers\Logistic\Inventory\StockTransfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.transfer.index');
    }
}
