<?php

namespace App\Http\Controllers\Author\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Book\CategoryResource;
use App\Models\Book\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display the list resource.
     */
        public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }
    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {

        return response()->json([
        'data'    => CategoryResource::make($category)]);
    }

}
