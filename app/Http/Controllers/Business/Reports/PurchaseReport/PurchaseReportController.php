<?php

namespace App\Http\Controllers\Business\Reports\PurchaseReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.purchase.index');
    }
}
