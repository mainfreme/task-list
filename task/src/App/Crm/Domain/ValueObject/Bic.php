<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class Bic extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $bic): self
    {
        return new self($bic);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('BIC cannot be empty');
        }

        if (strlen($this->value) > 255) {
            throw new InvalidArgumentException('BIC cannot exceed 255 characters');
        }

        // BIC format: 8-11 alphanumeric characters (same as SWIFT)
        if (!preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', strtoupper($this->value))) {
            throw new InvalidArgumentException('Invalid BIC format');
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
