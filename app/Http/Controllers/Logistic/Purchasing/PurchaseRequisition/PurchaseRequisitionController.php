<?php

namespace App\Http\Controllers\Logistic\Purchasing\PurchaseRequisition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    public function index()
    {
        return view('pages.logistic.purchasing.requisition.index');
    }
}
