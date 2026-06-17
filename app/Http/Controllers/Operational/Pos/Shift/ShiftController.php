<?php

namespace App\Http\Controllers\Operational\Pos\Shift;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        return view('pages.operational.pos.shift.index');
    }
}
