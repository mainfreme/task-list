<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class ClientRating
{
    private function __construct(
        private readonly ?int $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromInt(?int $rating): self
    {
        return new self($rating);
    }

    private function validate(int $value): void
    {
        if ($value < 1 || $value > 5) {
            throw new InvalidArgumentException('Client rating must be between 1 and 5');
        }
    }

    public function toInt(): ?int
    {
        return $this->value;
    }

    public function equals(ClientRating $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
