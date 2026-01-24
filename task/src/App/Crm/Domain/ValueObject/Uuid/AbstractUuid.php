<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject\Uuid;

use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class AbstractUuid
{
    protected function __construct(
        public readonly string $value
    ) {
        $this->validate($value);
    }

    /**
     * Generate a new UUID v7
     */
    public static function generate(): static
    {
        return new static(Str::uuid7()->toString());
    }

    /**
     * Create from existing UUID string
     */
    public static function fromString(string $uuid): static
    {
        return new static($uuid);
    }

    /**
     * Validate UUID format
     */
    protected function validate(string $value): void
    {
        if (!Str::isUuid($value)) {
            throw new InvalidArgumentException(
                sprintf('Invalid UUID format: %s', $value)
            );
        }
    }

    /**
     * Get UUID as string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get UUID as string (magic method)
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check if two UUIDs are equal
     */
    public function equals(AbstractUuid $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the UUID value
     */
    public function value(): string
    {
        return $this->value;
    }
}
