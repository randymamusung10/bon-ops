<?php

namespace App\Http\Controllers\Operational\Pos\SalesHistory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesHistoryController extends Controller
{
    public function index()
    {
        return view('pages.operational.pos.history.index');
    }
}
