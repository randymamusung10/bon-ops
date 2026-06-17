<?php

namespace App\Http\Controllers\Logistic\Master\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.company.index');
    }
}
