<?php

namespace App\Http\Controllers\Business\Reports\StockReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.stock.index');
    }
}
