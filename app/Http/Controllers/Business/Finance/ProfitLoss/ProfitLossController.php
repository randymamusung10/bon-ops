<?php

namespace App\Http\Controllers\Business\Finance\ProfitLoss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.profit_loss.index');
    }
}
