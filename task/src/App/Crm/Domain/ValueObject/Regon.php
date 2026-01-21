<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class Regon
{
    private function __construct(
        private readonly ?string $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromString(?string $regon): self
    {
        return new self($regon);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('REGON cannot be empty');
        }

        // Remove spaces and dashes for validation
        $cleaned = preg_replace('/[\s-]/', '', $value);

        // Polish REGON validation: 9 or 14 digits
        if (!preg_match('/^\d{9}$|^\d{14}$/', $cleaned)) {
            throw new InvalidArgumentException('Invalid REGON format. REGON must contain 9 or 14 digits');
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

    public function equals(Regon $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
