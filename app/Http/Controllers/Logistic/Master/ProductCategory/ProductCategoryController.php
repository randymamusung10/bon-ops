<?php

namespace App\Http\Controllers\Logistic\Master\ProductCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        return view('pages.logistic.master.category.index');
    }
}
