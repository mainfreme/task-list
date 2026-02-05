<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final class Email extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(string $value)
    {
        parent::__construct($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new \InvalidArgumentException('Email cannot be empty');
        }

        if (filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }
}