<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class Bic
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $bic): self
    {
        return new self($bic);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('BIC cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('BIC cannot exceed 255 characters');
        }

        // BIC format: 8-11 alphanumeric characters (same as SWIFT)
        if (!preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', strtoupper($value))) {
            throw new InvalidArgumentException('Invalid BIC format');
        }
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Bic $other): bool
    {
        return strtoupper($this->value) === strtoupper($other->value);
    }
}
