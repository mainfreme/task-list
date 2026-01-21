<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class TagColor
{
    private function __construct(
        private readonly ?string $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromString(?string $color): self
    {
        return new self($color);
    }

    private function validate(string $value): void
    {
        if (strlen($value) > 7) {
            throw new InvalidArgumentException('Tag color cannot exceed 7 characters');
        }

        // Hex color validation (format: #RRGGBB - exactly 7 characters including #)
        if (!preg_match('/^#[A-Fa-f0-9]{6}$/', $value)) {
            throw new InvalidArgumentException('Tag color must be a valid hex color code in format #RRGGBB (e.g., #FF5733)');
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

    public function equals(TagColor $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
