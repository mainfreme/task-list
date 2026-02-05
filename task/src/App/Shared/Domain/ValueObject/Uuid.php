<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class Uuid
{
    private function __construct(
        public readonly string $value
    ) {
        $this->validate($value);
    }

    public static function generate(): self
    {
        return new self(Str::uuid7()->toString());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Uuid $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(string $value): void
    {
        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $value)) {
            throw new InvalidArgumentException('Application ID must be a valid UUID');
        }
    }
}
