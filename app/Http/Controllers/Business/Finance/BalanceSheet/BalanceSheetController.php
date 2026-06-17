<?php

namespace App\Http\Controllers\Business\Finance\BalanceSheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.balance_sheet.index');
    }
}
