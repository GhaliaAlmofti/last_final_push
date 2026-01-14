<?php

namespace App\Http\Controllers\Admin\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Book\CreateBookRequest;
use App\Http\Requests\Admin\Book\UpdateBookRequest;
use App\Http\Resources\Admin\Book\BookResource;
use App\Models\Book\Book;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Admin Book endpoints
     *
     * @group Admin - Books
     * @authenticated
     */
    //protected BookService $bookService;

    //in this task no use of srrvice
    // public function __construct(BookService $bookService)
    // {
    //     $this->bookService = $bookService;
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $books = QueryBuilder::for(Book::with(['category', 'authors', 'cover', 'owner']))
            ->allowedFilters([
                AllowedFilter::partial('title'),
                AllowedFilter::partial('isbn'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('is_active'),
            ])
            ->allowedSorts(['title', 'price', 'publish_year', 'created_at'])
            ->defaultSort('-created_at')
            ->allowedIncludes(['category', 'authors', 'cover'])
            ->paginate($request->integer('per_page', 15))
            ->appends($request->query());

        return $this->sendResponse(
            BookResource::collection($books)->response()->getData(true),
            'Books retrieved successfully.'
        );
    }


    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load([
            'category',
            'authors',
            'cover',
            'owner'
        ]);

        /**
         * Display the specified resource.
         *
         * @response 200 {
         *  "data": {"id":1,"title":"The Great Novel"},
         *  "message": "Book retrieved successfully",
         *  "errors": null
         * }
         */
        return response()->json([
            'data' => BookResource::make($book),
            'message' => 'Book retrieved successfully',
            'errors' => null
        ]);
    }
    /**
     * store the specified resource.
     */

    public function store(CreateBookRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();
            $book = Book::create(Arr::except($validatedData, ['author_ids', 'cover']));

            $syncData = [];

            $syncData[$validatedData['owner_id']] = ['is_owner' => true];

            if (!empty($validatedData['author_ids'])) {
                foreach ($validatedData['author_ids'] as $id) {
                    $syncData[$id] = ['is_owner' => false];
                }
            }

            $book->authors()->sync($syncData);

            if ($request->hasFile('cover')) {
                $file = $request->file('cover');

                $path = $file->store('books/covers', 'public');

                $book->cover()->create([
                    'file_path'  => $path,
                    'file_name'  => $file->getClientOriginalName(),
                    'mime_type'  => $file->getMimeType(),
                    'size'       => $file->getSize(),
                    'collection' => 'cover',
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return $this->sendResponse(
            BookResource::make($book->load(['authors', 'category', 'cover'])),
            'Book created successfully.',
            201
        );
    }

    /**
     * update the specified resource.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $book->update(Arr::except($validatedData, ['author_ids', 'owner_id', 'cover']));

            $syncData = collect($validatedData['author_ids'] ?? [])
                ->mapWithKeys(fn($id) => [$id => ['is_owner' => false]])
                ->put($validatedData['owner_id'], ['is_owner' => true])
                ->toArray();

            $book->authors()->sync($syncData);

            if ($request->hasFile('cover')) {
                if ($book->cover) {
                    Storage::disk('public')->delete($book->cover->file_path);
                    $book->cover()->delete();
                }

                $file = $request->file('cover');
                $path = $file->store('books/covers', 'public');

                $book->cover()->create([
                    'file_path'  => $path,
                    'file_name'  => $file->getClientOriginalName(),
                    'mime_type'  => $file->getMimeType(),
                    'size'       => $file->getSize(),
                    'collection' => 'cover',
                ]);
            }

            DB::commit();

            return $this->sendResponse(
                BookResource::make($book->load(['authors', 'category', 'cover'])),
                'Book updated successfully.'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Update failed for Book {$book->slug}: " . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Remove the specified book (Soft Delete).
     */
    public function destroy(Book $book)
    {
        try {

            DB::transaction(function () use ($book) {
                $book->delete();
            });

            return $this->sendResponse(
                null,
                'Book deleted successfully (Soft Delete).'
            );
        } catch (\Throwable $e) {
            Log::error("Delete failed for Book ID {$book->id}: " . $e->getMessage());
            return $this->sendError('Delete failed.', ['error' => 'Server Error'], 500);
        }
    }
}
