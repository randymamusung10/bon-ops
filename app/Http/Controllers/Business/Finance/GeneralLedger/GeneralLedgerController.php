<?php

namespace App\Http\Controllers\Business\Finance\GeneralLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.ledger.index');
    }
}
