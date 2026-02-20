<?php

declare(strict_types=1);

namespace App\Profile\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;

final class ProfileName extends AbstractValueObject implements ValueObjectInterface
{
    public function __construct(
        public readonly string $value,
    ) {}

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}