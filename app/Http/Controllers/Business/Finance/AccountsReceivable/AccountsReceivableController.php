<?php

namespace App\Http\Controllers\Business\Finance\AccountsReceivable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountsReceivableController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.receivable.index');
    }
}
