<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

final class RelationshipNotes
{
    private function __construct(
        private readonly ?string $value
    ) {
    }

    public static function fromString(?string $notes): self
    {
        return new self($notes);
    }

    public function toString(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }

    public function equals(RelationshipNotes $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
