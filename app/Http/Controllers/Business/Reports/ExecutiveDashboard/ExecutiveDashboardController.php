<?php

namespace App\Http\Controllers\Business\Reports\ExecutiveDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.executive.index');
    }
}
