<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HasOneOwner implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // $value is the 'authors' array from the request
        $ownerCount = collect($value)->where('is_owner', true)->count();

        if ($ownerCount === 0) {
            $fail('The book must have at least one owner.');
        }

        if ($ownerCount > 1) {
            $fail('The book cannot have more than one owner.');
        }
    }
}
