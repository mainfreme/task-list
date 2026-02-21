<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class SwiftCode extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $swiftCode): self
    {
        return new self($swiftCode);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('SWIFT code cannot be empty');
        }

        if (strlen($this->value) > 255) {
            throw new InvalidArgumentException('SWIFT code cannot exceed 255 characters');
        }

        // SWIFT code format: 8-11 alphanumeric characters
        if (!preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', strtoupper($this->value))) {
            throw new InvalidArgumentException('Invalid SWIFT code format');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return strtoupper($this->value) === strtoupper($other->getValue());
    }
}
