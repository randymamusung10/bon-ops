<?php

namespace App\Http\Controllers\Logistic\Master\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.customer.index');
    }
}
