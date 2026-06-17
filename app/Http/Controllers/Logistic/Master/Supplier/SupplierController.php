<?php

namespace App\Http\Controllers\Logistic\Master\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.supplier.index');
    }
}
