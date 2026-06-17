<?php

namespace App\Http\Controllers\System\Settings\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('pages.system.settings.roles.index');
    }
}
