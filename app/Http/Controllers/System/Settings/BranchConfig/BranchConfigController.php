<?php

namespace App\Http\Controllers\System\Settings\BranchConfig;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchConfigController extends Controller
{
    public function index()
    {
        return view('pages.system.settings.branch_config.index');
    }
}
