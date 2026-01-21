<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObjectInterface;
use App\Shared\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

final class HouseNumber extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        private readonly string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $houseNumber): self
    {
        return new self($houseNumber);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('House number cannot be empty');
        }

        if (strlen($this->value) > 10) {
            throw new InvalidArgumentException('House number cannot exceed 10 characters');
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
