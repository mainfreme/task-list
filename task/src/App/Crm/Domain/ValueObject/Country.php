<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class Country
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $country): self
    {
        return new self($country);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Country cannot be empty');
        }

        // Country limit: 100 in addresses, 255 in clients - use 100 for stricter validation
        // If used in Client context, it will still work (100 < 255)
        if (strlen($value) > 100) {
            throw new InvalidArgumentException('Country cannot exceed 100 characters');
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

    public function equals(Country $other): bool
    {
        return $this->value === $other->value;
    }
}
