<?php

namespace App\Http\Controllers\Business\Crm\CrmLoyalty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmLoyaltyController extends Controller
{
    public function index()
    {
        return view('pages.business.crm.loyalty.index');
    }
}
