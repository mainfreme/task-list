<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class Password extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(string $value)
    {
        parent::__construct($value);
        $this->validate();
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
        if (empty($this->value)) {
            throw new InvalidArgumentException('Password cannot be empty');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
