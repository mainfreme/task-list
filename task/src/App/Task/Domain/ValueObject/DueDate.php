<?php

declare(strict_types=1);

namespace App\Task\Domain\ValueObject;

use DateTimeImmutable;
use InvalidArgumentException;

final class DueDate
{
    private function __construct(
        private readonly DateTimeImmutable $value
    ) {
    }

    public static function fromDateTime(DateTimeImmutable $value): self
    {
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value)
            ?: DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if (!$dateTime) {
            throw new InvalidArgumentException('Invalid due date format. Expected Y-m-d H:i:s or Y-m-d');
        }

        return new self($dateTime);
    }

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::fromString($value);
    }

    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }

    public function format(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->value->format($format);
    }

    public function equals(DueDate $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }

    public function isPast(): bool
    {
        return $this->value < new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
