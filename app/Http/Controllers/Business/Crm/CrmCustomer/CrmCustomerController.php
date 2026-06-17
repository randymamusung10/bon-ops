<?php

namespace App\Http\Controllers\Business\Crm\CrmCustomer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmCustomerController extends Controller
{
    public function index()
    {
        return view('pages.business.crm.customer.index');
    }
}
