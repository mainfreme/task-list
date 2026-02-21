<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

interface ValueObjectInterface
{
    public function getValue(): string;

    public function equals(ValueObjectInterface $other): bool;

    public function validate(): void;
}
