<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class PostalCode
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $postalCode): self
    {
        return new self($postalCode);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Postal code cannot be empty');
        }

        if (strlen($value) > 20) {
            throw new InvalidArgumentException('Postal code cannot exceed 20 characters');
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

    public function equals(PostalCode $other): bool
    {
        return $this->value === $other->value;
    }
}
