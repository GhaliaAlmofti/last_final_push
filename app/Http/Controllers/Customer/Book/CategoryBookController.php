<?php

namespace App\Http\Controllers\Customer\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Book\CategoryResource;
use App\Models\Book\Category;
use Illuminate\Http\Request;

class CategoryBookController extends Controller
{
        /**
     * Display the list resource.
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return response()->json([
            'data' => CategoryResource::collection($categories),
            'message' => 'Categories retrieved successfully',
            'errors' => null
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function subcategories(Category $category)
    {
        $subcategories = $category->children;

        return response()->json([
            'data' => CategoryResource::collection($subcategories),
            'message' => 'Subcategories retrieved successfully',
            'errors' => null
        ]);
    }
}
