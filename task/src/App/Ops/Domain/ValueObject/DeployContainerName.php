<?php

declare(strict_types=1);

namespace App\Ops\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class DeployContainerName extends AbstractValueObject implements ValueObjectInterface
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

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

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
        if (strlen($this->value) > 255) {
            throw new InvalidArgumentException('Deploy container name cannot exceed 255 characters');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
