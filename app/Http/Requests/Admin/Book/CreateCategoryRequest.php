<?php

namespace App\Http\Requests\Admin\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCategoryRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:100|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'slug' => ['nullable', 'nullable', 'string', 'max:255', 'unique:categories,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }

    /**
     * Provide explicit body parameter definitions for Scribe.
     */
    public static function bodyParameters(): array
    {
        return [
            'name' => ['description' => 'Category name', 'example' => 'Science Fiction'],
            'parent_id' => ['description' => 'Parent category id (optional)', 'example' => null],
            'slug' => ['description' => 'URL-friendly slug', 'example' => 'science-fiction'],
        ];
    }

    public function messages()
    {
        return [
            'slug.regex' => 'The slug must be lowercase, alphanumeric, and use dashes as separators (e.g., science-fiction).',
        ];
    }
}
