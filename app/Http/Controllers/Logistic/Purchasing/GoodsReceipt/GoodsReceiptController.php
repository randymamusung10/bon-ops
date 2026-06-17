<?php

namespace App\Http\Controllers\Logistic\Purchasing\GoodsReceipt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function index()
    {
        return view('pages.logistic.purchasing.receipt.index');
    }
}
