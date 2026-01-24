<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObjectInterface;
use App\Shared\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

final class NoteContent extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        public readonly string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $content): self
    {
        return new self($content);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('Note content cannot be empty');
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
