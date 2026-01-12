<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class HouseNumber
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $houseNumber): self
    {
        return new self($houseNumber);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('House number cannot be empty');
        }

        if (strlen($value) > 10) {
            throw new InvalidArgumentException('House number cannot exceed 10 characters');
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

    public function equals(HouseNumber $other): bool
    {
        return $this->value === $other->value;
    }
}
