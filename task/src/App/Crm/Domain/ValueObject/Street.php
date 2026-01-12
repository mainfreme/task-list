<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class Street
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $street): self
    {
        return new self($street);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Street cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Street cannot exceed 255 characters');
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

    public function equals(Street $other): bool
    {
        return $this->value === $other->value;
    }
}
