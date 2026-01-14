<?php

namespace App\Http\Controllers\Admin\Book;

use App\Http\Requests\Admin\Book\CreateCategoryRequest;
use App\Http\Requests\Admin\Book\UpdateCategoryRequest;
use App\Http\Resources\Admin\Book\CategoryResource;
use App\Models\Book\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Str;

class CategoryBookController extends Controller
{
    /**
     * Listing
     */
    public function index()
    {
        $categories = QueryBuilder::for(Category::class)
            ->allowedIncludes(['parent', 'children', 'childrenRecursive', 'books'])
            
            ->allowedFilters([
                'name',
                'slug',
                AllowedFilter::exact('parent_id'),
                AllowedFilter::scope('roots'),
            ])
            
            ->defaultSort('order_column')
            ->allowedSorts(['name', 'order_column', 'created_at'])
            
            ->paginate(request()->get('per_page', 15))
            ->appends(request()->query());

        return $this->sendResponse(
            CategoryResource::collection($categories)->response()->getData(true),
            'Categories retrieved successfully.'
        );
    }

    /**
     * Store Category/Subcategory
     */
    public function store(CreateCategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return $this->sendResponse(
            new CategoryResource($category),
            'Category created successfully.',
            201
        );
    }

    /**
     * Display using Route Model Binding (resolves via slug)
     */
    public function show(Category $category)
    {
        $category->load(['childrenRecursive', 'books', 'parent']);

        return $this->sendResponse(new CategoryResource($category), 'Category details.');
    }

    /**
     * Update Category
     */
    public function update(UpdateCategoryRequest $request ,Category $category)
    {
        $category->update($request->validated());

        return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');
    }

    /**
     *   Delete
     */
    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return $this->sendError('Delete subcategories first.', [], 422);
        }

        if ($category->books()->exists()) {
            return $this->sendError('Cannot delete category containing books.', [], 422);
        }

        $category->delete();

        return $this->sendResponse(null, 'Category deleted successfully.');
    }
}
