<?php

declare(strict_types=1);

namespace Domain\ApplicationManager\ValueObject;

final class ApiKey
{
    private function __construct(
        private readonly string $value
    ) {
        if (empty($this->value)) {
            throw new \InvalidArgumentException('API Key cannot be empty');
        }

        if (strlen($this->value) < 32) {
            throw new \InvalidArgumentException('API Key must be at least 32 characters long');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(32)));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ApiKey $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

