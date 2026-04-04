<?php

declare(strict_types=1);

namespace App\Ops\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class DeployRepository extends AbstractValueObject implements ValueObjectInterface
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
        if (trim($this->value) === '') {
            throw new InvalidArgumentException('Deploy repository cannot be empty');
        }

        if (strlen($this->value) > 500) {
            throw new InvalidArgumentException('Deploy repository cannot exceed 500 characters');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
