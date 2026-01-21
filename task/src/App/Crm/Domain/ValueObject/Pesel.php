<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;

final class Pesel
{
    private function __construct(
        private readonly ?string $value
    ) {
        if ($value !== null) {
            $this->validate($value);
        }
    }

    public static function fromString(?string $pesel): self
    {
        return new self($pesel);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('PESEL cannot be empty');
        }

        // Polish PESEL validation: 11 digits
        if (!preg_match('/^\d{11}$/', $value)) {
            throw new InvalidArgumentException('Invalid PESEL format. PESEL must contain exactly 11 digits');
        }

        // PESEL checksum validation
        if (!$this->validateChecksum($value)) {
            throw new InvalidArgumentException('Invalid PESEL checksum');
        }
    }

    private function validateChecksum(string $pesel): bool
    {
        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$pesel[$i] * $weights[$i];
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $checksum === (int)$pesel[10];
    }

    public function toString(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }

    public function equals(Pesel $other): bool
    {
        return $this->value === $other->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }
}
