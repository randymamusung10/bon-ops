<?php

namespace App\Http\Controllers\Operational\Restaurant\TableManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TableManagementController extends Controller
{
    public function index()
    {
        return view('pages.operational.restaurant.tables.index');
    }
}
