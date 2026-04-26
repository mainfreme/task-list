<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Rules;

use App\Crm\Domain\ValueObject\Nip;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

/**
 * Odrzuca niewłaściwy NIP w odpowiedzi 422 zamiast propagować wyjątki z ValueObject.
 */
final class NipValue implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        try {
            Nip::fromString($value);
        } catch (InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
