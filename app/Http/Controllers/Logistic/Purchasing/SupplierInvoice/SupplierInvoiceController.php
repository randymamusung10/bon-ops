<?php

namespace App\Http\Controllers\Logistic\Purchasing\SupplierInvoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierInvoiceController extends Controller
{
    public function index()
    {
        return view('pages.logistic.purchasing.invoice.index');
    }
}
