<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class SwiftCode
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $swiftCode): self
    {
        return new self($swiftCode);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('SWIFT code cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('SWIFT code cannot exceed 255 characters');
        }

        // SWIFT code format: 8-11 alphanumeric characters
        if (!preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', strtoupper($value))) {
            throw new InvalidArgumentException('Invalid SWIFT code format');
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

    public function equals(SwiftCode $other): bool
    {
        return strtoupper($this->value) === strtoupper($other->value);
    }
}
