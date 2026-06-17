<?php

namespace App\Http\Controllers\Logistic\Master\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.unit.index');
    }
}
