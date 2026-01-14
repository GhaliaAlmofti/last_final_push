<?php

namespace App\Http\Requests\Author\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterOwnerRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'unique:users'],
            'password' => ['required'],
            'bio'      => ['nullable', 'string', 'max:1000'],
            'country'  => ['nullable', 'string', 'max:100'],
            'photo'      => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    /**
     * Provide explicit body parameter definitions for Scribe.
     */
    public static function bodyParameters(): array
    {
        return [
            'first_name' => ['description' => 'Owner first name', 'example' => 'Alice'],
            'last_name' => ['description' => 'Owner last name', 'example' => 'Smith'],
            'username' => ['description' => 'Unique username', 'example' => 'owner1'],
            'password' => ['description' => 'Account password', 'example' => 'secret123'],
            'bio' => ['description' => 'Short biography', 'example' => 'Event organizer'],
            'country' => ['description' => 'Country name', 'example' => 'Egypt'],
            'photo' => ['description' => 'Avatar image file', 'example' => null],
        ];
    }
}
