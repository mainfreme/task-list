<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class Latitude
{
    private function __construct(
        private readonly ?float $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromFloat(?float $latitude): self
    {
        return new self($latitude);
    }

    private function validate(float $value): void
    {
        if ($value < -90.0 || $value > 90.0) {
            throw new InvalidArgumentException('Latitude must be between -90.0 and 90.0');
        }

        // Validate precision: decimal(10,8) - max 8 decimal places
        $valueStr = (string)$value;
        if (strpos($valueStr, '.') !== false) {
            $parts = explode('.', $valueStr);
            $decimalPlaces = strlen($parts[1] ?? '');
            if ($decimalPlaces > 8) {
                throw new InvalidArgumentException('Latitude cannot have more than 8 decimal places');
            }
        }
    }

    public function toFloat(): ?float
    {
        return $this->value;
    }

    public function equals(Latitude $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
