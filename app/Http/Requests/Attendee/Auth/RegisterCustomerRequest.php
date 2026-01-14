<?php

namespace App\Http\Requests\Attendee\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\PhoneNumber;


class RegisterAttendeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

   protected function prepareForValidation(): void
    {
        if ($this->filled('phone_number')) {
            $this->formatPhoneNumber();
        }
    }

    private function formatPhoneNumber(): void
    {
        try {
            $this->merge([
                'phone_number' => (string) PhoneNumber::make($this->phone_number, 'LY')->formatE164(),
            ]);
        } catch (\Exception) {
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username'   => ['required', 'string', 'unique:users,username', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'password'   => ['required', 'string', 'min:8'],
            'email'        => ['required', 'email'],
            'phone_number' => ['nullable','phone:LY,mobile','unique:customers,phone_number'],
        ];
    }

        public function messages(): array
    {
        return [
            'phone_number.phone' => 'The phone number must be a valid Libyan mobile number (e.g., 091.., 092.., 094..).',
        ];
    }
}
