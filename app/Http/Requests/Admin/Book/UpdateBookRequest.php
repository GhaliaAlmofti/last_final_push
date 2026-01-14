<?php

namespace App\Http\Requests\Admin\Book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $book = $this->route('book');
        $isbnRule = $book ? "required|string|unique:books,isbn,{$book->id}" : 'required|string|unique:books,isbn';

        return [
            'title'        => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'isbn'         => $isbnRule,
            'price'        => 'required|numeric|min:0',
            'publish_year' => 'required|integer|min:1000|max:' . date('Y'),
            'stock'        => 'required|integer|min:0',
            'owner_id'     => ['required', 'exists:users,id'],
            'author_ids'   => ['nullable', 'array'],
            'author_ids.*' => ['exists:users,id', 'different:owner_id'],
            'cover'        => 'nullable|image|max:5120',
        ];
    }

    /**
     * Provide explicit body parameter definitions for Scribe.
     */
    public static function bodyParameters(): array
    {
        return [
            'title' => ['description' => 'Book title', 'example' => 'Updated Title'],
            'category_id' => ['description' => 'Category id', 'example' => 1],
            'isbn' => ['description' => 'Unique ISBN', 'example' => '9781234567897'],
            'price' => ['description' => 'Book price', 'example' => 12.99],
            'publish_year' => ['description' => 'Year of publication', 'example' => 2024],
            'stock' => ['description' => 'Available stock count', 'example' => 5],
            'owner_id' => ['description' => 'Owner user id', 'example' => 2],
            'author_ids' => ['description' => 'Array of author ids', 'example' => [3, 4]],
            'author_ids.*' => ['description' => 'Author id', 'example' => 3],
            'cover' => ['description' => 'Cover image file', 'example' => null],
        ];
    }
}
