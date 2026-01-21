<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

final class IsPrimary
{
    private function __construct(
        private readonly bool $value
    ) {
    }

    public static function fromBool(bool $isPrimary): self
    {
        return new self($isPrimary);
    }

    public function toBool(): bool
    {
        return $this->value;
    }

    public function equals(IsPrimary $other): bool
    {
        return $this->value === $other->value;
    }
}
