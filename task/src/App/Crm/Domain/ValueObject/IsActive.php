<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

final class IsActive
{
    private function __construct(
        private readonly bool $value
    ) {
    }

    public static function fromBool(bool $isActive): self
    {
        return new self($isActive);
    }

    public function toBool(): bool
    {
        return $this->value;
    }

    public function equals(IsActive $other): bool
    {
        return $this->value === $other->value;
    }
}
