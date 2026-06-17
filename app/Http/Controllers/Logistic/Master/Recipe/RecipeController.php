<?php

namespace App\Http\Controllers\Logistic\Master\Recipe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.recipe.index');
    }
}
