<?php

namespace App\Http\Controllers\Business\Crm\CrmMembership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmMembershipController extends Controller
{
    public function index()
    {
        return view('pages.business.crm.membership.index');
    }
}
