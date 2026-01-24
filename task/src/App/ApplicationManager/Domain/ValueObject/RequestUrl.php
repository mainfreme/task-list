<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\ValueObject;

use InvalidArgumentException;

final class RequestUrl
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return new self($value);
    }

    private function validate(string $value): void
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException('Request URL cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Request URL cannot exceed 255 characters');
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Request URL must be a valid URL');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(RequestUrl $other): bool
    {
        return $this->value === $other->value;
    }
}
