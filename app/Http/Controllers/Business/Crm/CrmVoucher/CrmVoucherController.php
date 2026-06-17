<?php

namespace App\Http\Controllers\Business\Crm\CrmVoucher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmVoucherController extends Controller
{
    public function index()
    {
        return view('pages.business.crm.voucher.index');
    }
}
