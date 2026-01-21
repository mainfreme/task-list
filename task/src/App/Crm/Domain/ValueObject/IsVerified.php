<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

final class IsVerified
{
    private function __construct(
        private readonly bool $value
    ) {
    }

    public static function fromBool(bool $isVerified): self
    {
        return new self($isVerified);
    }

    public function toBool(): bool
    {
        return $this->value;
    }

    public function equals(IsVerified $other): bool
    {
        return $this->value === $other->value;
    }
}
