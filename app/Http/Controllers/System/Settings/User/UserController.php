<?php

namespace App\Http\Controllers\System\Settings\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.system.settings.users.index');
    }
}
