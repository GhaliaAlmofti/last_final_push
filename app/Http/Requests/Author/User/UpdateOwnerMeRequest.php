<?php

namespace App\Http\Requests\Author\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOwnerMeRequest extends FormRequest
{
    /**
     * Form request used to update authenticated organizer profile.
     *
     * This request is limited to users with the `organizer` type.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()?->type?->is('organizer');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'username' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'first_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'bio' => [

                'nullable',
                'string',
                'max:500',
            ],
            'country' => [

                'nullable',
                'string',
                'max:100',
            ],
            'photo' => [

                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'username.unique' => 'This username is already taken.',
            'username.max' => 'Username cannot exceed 255 characters.',
            'first_name.required' => 'First name is required.',
            'photo.image' => 'Photo must be a valid image.',
            'photo.mimes' => 'Photo must be JPEG, PNG, JPG, GIF, or WebP.',
            'photo.max' => 'Photo size cannot exceed 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => trim($this->first_name ?? ''),
            'last_name' => trim($this->last_name ?? ''),
            'username' => trim($this->username ?? ''),
        ]);
    }

    /**
     * Provide explicit body parameter definitions for Scribe/OpenAPI.
     */
    public static function bodyParameters(): array
    {
        return [
            'username' => ['description' => 'New username (optional)', 'example' => 'newowner1'],
            'first_name' => ['description' => 'First name (optional)', 'example' => 'New Alice'],
            'last_name' => ['description' => 'Last name (optional)', 'example' => 'New Smith'],
            'bio' => ['description' => 'Short biography (optional)', 'example' => 'Event organizer'],
            'country' => ['description' => 'Country (optional)', 'example' => 'UAE'],
            'photo' => ['description' => 'Avatar image file (optional)', 'example' => null],
        ];
    }
}
