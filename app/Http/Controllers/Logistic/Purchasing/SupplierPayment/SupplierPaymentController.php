<?php

namespace App\Http\Controllers\Logistic\Purchasing\SupplierPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function index()
    {
        return view('pages.logistic.purchasing.payment.index');
    }
}
