<?php

namespace App\Http\Controllers\Business\Finance\AccountsPayable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountsPayableController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.payable.index');
    }
}
