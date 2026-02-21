<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class ApartmentNumber extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $apartmentNumber): self
    {
        return new self($apartmentNumber);
    }

    public function validate(): void
    {
        if (strlen($this->value) > 15) {
            throw new InvalidArgumentException('Apartment number cannot exceed 15 characters');
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
