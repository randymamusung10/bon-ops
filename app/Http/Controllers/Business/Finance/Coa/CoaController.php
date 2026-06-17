<?php

namespace App\Http\Controllers\Business\Finance\Coa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.coa.index');
    }
}
