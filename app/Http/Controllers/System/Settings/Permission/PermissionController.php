<?php

namespace App\Http\Controllers\System\Settings\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        return view('pages.system.settings.permissions.index');
    }
}
