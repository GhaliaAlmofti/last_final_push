<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username'    => 'required|string',
            'password'    => 'required|string',
        ];
    }

    /**
     * Provide explicit body parameter definitions for Scribe.
     */
    public static function bodyParameters(): array
    {
        return [
            'username' => ['description' => 'Username or email used to login', 'example' => 'owner1'],
            'password' => ['description' => 'Account password', 'example' => 'secret123'],
        ];
    }
}
