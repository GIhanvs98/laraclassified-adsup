<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxCharacters implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $maxLength;

    public function __construct($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plainTextValue = strip_tags(html_entity_decode($value));

        $length = mb_strlen($plainTextValue, "UTF-8");

        if ($length > $this->maxLength) {

            $fail("The :attribute must not exceed {$this->maxLength} characters.");
        }
    }
}
