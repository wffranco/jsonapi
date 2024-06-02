<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');
        } elseif ($this->containsInvalidCharacters($value)) {
            $fail('The :attribute must contain only lowercase letters, numbers, and hyphens.');
        } elseif ($this->startsWithHyphen($value)) {
            $fail('The :attribute must not start with a hyphen.');
        } elseif ($this->endsWithHyphen($value)) {
            $fail('The :attribute must not end with a hyphen.');
        } elseif ($this->hasConsecutiveHyphens($value)) {
            $fail('The :attribute must not contain consecutive hyphens.');
        }
    }

    protected function startsWithHyphen(string $value): bool
    {
        return $value[0] === '-';
    }

    protected function endsWithHyphen(string $value): bool
    {
        return substr($value, -1) === '-';
    }

    protected function hasConsecutiveHyphens(string $value): bool
    {
        return strpos($value, '--') !== false;
    }

    protected function containsInvalidCharacters(string $value): bool
    {
        return preg_match('/[^a-z0-9-]/', $value);
    }
}
