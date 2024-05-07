<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinCharacters implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    protected $minLength;

    public function __construct($minLength)
    {
        $this->minLength = $minLength;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plainTextValue = strip_tags(html_entity_decode($value));

        $length = mb_strlen($plainTextValue, "UTF-8");

        if ($length < $this->minLength) {

            $fail("The :attribute must be at least {$this->minLength} characters.");
        }
    }
}
