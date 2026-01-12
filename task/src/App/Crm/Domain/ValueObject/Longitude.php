<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class Longitude
{
    private function __construct(
        private readonly ?float $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromFloat(?float $longitude): self
    {
        return new self($longitude);
    }

    private function validate(float $value): void
    {
        if ($value < -180.0 || $value > 180.0) {
            throw new InvalidArgumentException('Longitude must be between -180.0 and 180.0');
        }

        // Validate precision: decimal(11,8) - max 8 decimal places
        $valueStr = (string)$value;
        if (strpos($valueStr, '.') !== false) {
            $parts = explode('.', $valueStr);
            $decimalPlaces = strlen($parts[1] ?? '');
            if ($decimalPlaces > 8) {
                throw new InvalidArgumentException('Longitude cannot have more than 8 decimal places');
            }
        }
    }

    public function toFloat(): ?float
    {
        return $this->value;
    }

    public function equals(Longitude $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
