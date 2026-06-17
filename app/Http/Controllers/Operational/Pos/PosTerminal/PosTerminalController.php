<?php

namespace App\Http\Controllers\Operational\Pos\PosTerminal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PosTerminalController extends Controller
{
    public function index()
    {
        return view('pages.operational.pos.terminal.index');
    }
}
