<?php

namespace App\Http\Controllers\Business\Reports\SalesReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.sales.index');
    }
}
