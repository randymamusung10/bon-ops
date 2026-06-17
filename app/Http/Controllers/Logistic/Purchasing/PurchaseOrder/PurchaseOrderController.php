<?php

namespace App\Http\Controllers\Logistic\Purchasing\PurchaseOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('pages.logistic.purchasing.order.index');
    }
}
