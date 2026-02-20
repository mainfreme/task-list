<?php

declare(strict_types=1);

namespace App\Profile\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractUuid;
use Illuminate\Support\Str;

final class ProfileId extends AbstractUuid
{
    /**
     * Generate a new UUID v7
     */
    public static function generate(): static
    {
        return new static(Str::uuid7()->toString());
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function getValue(): string
    {
        return $this->getValue();
    }

}
