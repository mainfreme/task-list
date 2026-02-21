<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class Street extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $street): self
    {
        return new self($street);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('Street cannot be empty');
        }

        if (strlen($this->value) > 255) {
            throw new InvalidArgumentException('Street cannot exceed 255 characters');
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
