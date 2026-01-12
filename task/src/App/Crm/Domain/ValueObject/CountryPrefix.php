<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class CountryPrefix
{
    private function __construct(
        private readonly ?string $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromString(?string $countryPrefix): self
    {
        return new self($countryPrefix);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Country prefix cannot be empty');
        }

        if (strlen($value) > 5) {
            throw new InvalidArgumentException('Country prefix cannot exceed 5 characters');
        }
    }

    public function toString(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }

    public function equals(CountryPrefix $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
