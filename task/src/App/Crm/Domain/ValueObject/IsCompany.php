<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

final class IsCompany
{
    private function __construct(
        private readonly bool $value
    ) {
    }

    public static function fromBool(bool $isCompany): self
    {
        return new self($isCompany);
    }

    public function toBool(): bool
    {
        return $this->value;
    }

    public function equals(IsCompany $other): bool
    {
        return $this->value === $other->value;
    }
}
