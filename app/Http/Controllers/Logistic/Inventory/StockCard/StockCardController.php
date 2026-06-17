<?php

namespace App\Http\Controllers\Logistic\Inventory\StockCard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockCardController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.card.index');
    }
}
