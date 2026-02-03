<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObjectInterface;
use App\Shared\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

final class CompanyAccountName extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $accountName): self
    {
        return new self($accountName);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('Company account name cannot be empty');
        }

        if (strlen($this->value) > 255) {
            throw new InvalidArgumentException('Company account name cannot exceed 255 characters');
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
