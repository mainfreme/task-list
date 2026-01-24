<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObjectInterface;
use App\Shared\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

final class City extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        public readonly string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $city): self
    {
        return new self($city);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('City cannot be empty');
        }

        if (strlen($this->value) > 100) {
            throw new InvalidArgumentException('City cannot exceed 100 characters');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $this->value === $other->getValue();
    }
}
