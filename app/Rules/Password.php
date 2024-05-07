<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Password implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
            $fail('The :attribute must contain at least one uppercase and one lowercase letter.');
        }

        if (!preg_match('/\pL/u', $value)) {
            $fail('The :attribute must contain at least one letter.');
        }

        if (!preg_match('/\p{Z}|\p{S}|\p{P}/u', $value)) {
            $fail('The :attribute must contain at least one symbol.');
        }

        if (!preg_match('/\pN/u', $value)) {
            $fail('The :attribute must contain at least one number.');
        }
    }
}
