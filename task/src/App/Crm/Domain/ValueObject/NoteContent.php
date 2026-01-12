<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class NoteContent
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $content): self
    {
        return new self($content);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Note content cannot be empty');
        }
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(NoteContent $other): bool
    {
        return $this->value === $other->value;
    }
}
