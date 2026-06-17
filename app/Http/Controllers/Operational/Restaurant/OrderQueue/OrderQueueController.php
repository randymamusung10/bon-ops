<?php

namespace App\Http\Controllers\Operational\Restaurant\OrderQueue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderQueueController extends Controller
{
    public function index()
    {
        return view('pages.operational.restaurant.queue.index');
    }
}
