<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObjectInterface;
use App\Shared\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

final class Country extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $country): self
    {
        return new self($country);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('Country cannot be empty');
        }

        // Country limit: 100 in addresses, 255 in clients - use 100 for stricter validation
        // If used in Client context, it will still work (100 < 255)
        if (strlen($this->value) > 100) {
            throw new InvalidArgumentException('Country cannot exceed 100 characters');
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
