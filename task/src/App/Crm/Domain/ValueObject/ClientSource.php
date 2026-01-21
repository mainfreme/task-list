<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class ClientSource
{
    private function __construct(
        private readonly ?string $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromString(?string $source): self
    {
        return new self($source);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Client source cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Client source cannot exceed 255 characters');
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

    public function equals(ClientSource $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
