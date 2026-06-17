<?php

namespace App\Http\Controllers\Business\Reports\FoodCostReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FoodCostReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.food_cost.index');
    }
}
