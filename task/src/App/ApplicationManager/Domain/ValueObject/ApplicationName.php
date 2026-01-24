<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;
use InvalidArgumentException;

final class ApplicationName extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        public readonly string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function validate(): void
    {
        if (trim($this->value) === '') {
            throw new InvalidArgumentException('Application name cannot be empty');
        }

        if (strlen($this->value) > 255) {
            throw new InvalidArgumentException('Application name cannot exceed 255 characters');
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
