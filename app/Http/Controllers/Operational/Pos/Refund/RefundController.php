<?php

namespace App\Http\Controllers\Operational\Pos\Refund;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        return view('pages.operational.pos.refund.index');
    }
}
