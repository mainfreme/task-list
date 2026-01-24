<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

final class IsDeleted
{
    private function __construct(
        private readonly bool $value
    ) {
    }

    public static function fromBool(bool $isDeleted): self
    {
        return new self($isDeleted);
    }

    public function toBool(): bool
    {
        return $this->value;
    }

    public function equals(IsDeleted $other): bool
    {
        return $this->value === $other->value;
    }
}
