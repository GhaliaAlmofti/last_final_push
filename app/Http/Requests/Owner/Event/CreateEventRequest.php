<?php

namespace App\Http\Requests\Owner\Event;

use App\Enums\Enum\Event\EventLoaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEventRequest extends FormRequest
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
        return
            [
                'title' => 'required|string|max:255',
                'location' => ['required', 'string', Rule::in(EventLoaction::cases())],
                'location_description' => 'required|string|max:500',
                'description' => 'required|string|max:2000',
                'start_date' => [
                    'required',
                    'date',
                    'date_format:Y-m-d H:i:s',
                    Rule::date()->after(now()),
                ],
                'end_date' => [
                    'required',
                    'date',
                    'date_format:Y-m-d H:i:s',
                    Rule::date()->after('start_date'),
                    Rule::date()->before(now()->addYears(2)),
                ],
                'max_attendees' => 'nullable|integer|min:1|max:100000',
                'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ];
    }

    /**
     * Provide explicit body parameter definitions for Scribe/OpenAPI.
     */
    public static function bodyParameters(): array
    {
        return [
            'title' => ['description' => 'Event title', 'example' => 'Summer Gala'],
            'location' => ['description' => 'Location enum', 'example' => 'venue'],
            'location_description' => ['description' => 'Human readable location', 'example' => 'Main hall, 2nd floor'],
            'description' => ['description' => 'Detailed event description', 'example' => 'An evening of music and food.'],
            'start_date' => ['description' => 'Start datetime (Y-m-d H:i:s)', 'example' => now()->addDays(10)->format('Y-m-d H:i:s')],
            'end_date' => ['description' => 'End datetime (Y-m-d H:i:s)', 'example' => now()->addDays(11)->format('Y-m-d H:i:s')],
            'max_attendees' => ['description' => 'Maximum attendees', 'example' => 500],
            'poster' => ['description' => 'Poster image file (jpeg,png,jpg,gif)', 'example' => null],
        ];
    }
}
