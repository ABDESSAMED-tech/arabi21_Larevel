<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {

        $categories = Category::whereNull('parent_id')
            ->orderBy('order', 'asc')
            ->get();

        return new CategoryCollection($categories);
    }
}
