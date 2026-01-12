<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class StateProvince
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $stateProvince): self
    {
        return new self($stateProvince);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('State/Province cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new InvalidArgumentException('State/Province cannot exceed 100 characters');
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

    public function equals(StateProvince $other): bool
    {
        return $this->value === $other->value;
    }
}
