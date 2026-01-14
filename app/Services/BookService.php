<?php

namespace App\Services;

use App\Models\Book\Book;
use Illuminate\Database\Eloquent\Builder;

class BookService
{
    public function getBooks(array $filters = [])
    {
        $query = Book::with(['category', 'subCategory', 'authors', 'cover']);

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('authors.user', function (Builder $q) use ($filters) {
                      $q->where('name', 'like', '%' . $filters['search'] . '%');
                  });
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('subCategory', function (Builder $q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        if (!empty($filters['subcategory_id'])) {
            $query->where('sub_category_id', $filters['subcategory_id']);
        }

        return $query->latest()->paginate(15);
    }

    
}
