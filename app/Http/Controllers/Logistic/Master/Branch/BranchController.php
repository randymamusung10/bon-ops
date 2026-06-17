<?php

namespace App\Http\Controllers\Logistic\Master\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.branch.index');
    }
}
