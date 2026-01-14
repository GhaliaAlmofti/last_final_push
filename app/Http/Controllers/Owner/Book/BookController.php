<?php

namespace App\Http\Controllers\Author\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Book\BookResource;
use App\Models\Book\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
 private function isBookOwner(Request $request, Book $book): bool
    {
        return $book->authors()
            ->where('user_id', $request->user()->id)
            ->wherePivot('is_owner', true)
            ->exists();
    }

    public function index(Request $request)
    {
        $books = $request->user()->author->books()
            ->with(['category']) 
            ->latest()
            ->paginate(10);
        return BookResource::collection($books)
            ->additional(['status' => 'success']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'publish_year'    => ['required', 'integer', 'digits:4'],
            'price'           => ['required', 'numeric', 'min:0'],
            'ISBN'            => ['required', 'string', 'unique:books,ISBN'],
            'category_id'     => ['required', 'exists:categories,id'],
            'stock'           => ['required', 'integer', 'min:0'],
            'co_author_ids'   => ['nullable', 'array'],
            'co_author_ids.*' => ['exists:authors,user_id'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $book = Book::create(collect($validated)->except('co_author_ids')->toArray());

            $syncData = collect($validated['co_author_ids'] ?? [])
                ->mapWithKeys(fn($id) => [$id => ['is_owner' => false]])
                ->put($request->user()->id, ['is_owner' => true])
                ->toArray();

            $book->authors()->sync($syncData);

            return (new BookResource($book->load('authors.user')))
                ->additional(['status' => 'success', 'message' => 'Book created.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    public function update(Request $request, Book $book)
    {
        if (!$this->isBookOwner($request, $book)) {
            return response()->json(['message' => 'Unauthorized. Only the owner can update.'], 403);
        }

        $book->update($request->only(['title', 'price', 'stock']));

        return response()->json([
            'status' => 'success',
            'message' => 'Book updated.',
            'data' => new BookResource($book)
        ]);
    }

    public function destroy(Request $request, Book $book)
    {
        if (!$this->isBookOwner($request, $book)) {
            return response()->json(['message' => 'Unauthorized. Only the owner can delete.'], 403);
        }

        if ($book->orderItems()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete book with sales history. Archive it instead.'
            ], 422);
        }

        $book->delete();

        return response()->json(null, 204);
    }
}
