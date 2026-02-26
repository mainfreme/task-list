<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class AbstractUuid
{
    public function __construct(
        public readonly string $value
    ) {
        $this->validate();
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
    protected function validate(): void
    {
        if (!Str::isUuid($this->getValue())) {
            throw new InvalidArgumentException(
                sprintf('Invalid UUID format: %s', $this->getValue())
            );
        }
    }

    /**
     * Get UUID as string
     */
    public function toString(): string
    {
        return $this->getValue();
    }

    /**
     * Get UUID as string (magic method)
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * Check if two UUIDs are equal
     */
    public function equals(AbstractUuid $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    /**
     * Get the UUID value
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
