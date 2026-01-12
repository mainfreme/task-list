<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class ApartmentNumber
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $apartmentNumber): self
    {
        return new self($apartmentNumber);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Apartment number cannot be empty');
        }

        if (strlen($value) > 15) {
            throw new InvalidArgumentException('Apartment number cannot exceed 15 characters');
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

    public function equals(ApartmentNumber $other): bool
    {
        return $this->value === $other->value;
    }
}
