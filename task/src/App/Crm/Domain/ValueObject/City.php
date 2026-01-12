<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class City
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $city): self
    {
        return new self($city);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('City cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new InvalidArgumentException('City cannot exceed 100 characters');
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

    public function equals(City $other): bool
    {
        return $this->value === $other->value;
    }
}
