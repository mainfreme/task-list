<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObjectInterface;
use App\Shared\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

final class Nip extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        public readonly string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $nip): self
    {
        return new self($nip);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('NIP cannot be empty');
        }

        // Remove spaces and dashes for validation
        $cleaned = preg_replace('/[\s-]/', '', $this->value);

        // Polish NIP validation: 10 digits
        if (!preg_match('/^\d{10}$/', $cleaned)) {
            throw new InvalidArgumentException('Invalid NIP format. NIP must contain 10 digits');
        }

        // NIP checksum validation
        if (!$this->validateChecksum($cleaned)) {
            throw new InvalidArgumentException('Invalid NIP checksum');
        }
    }

    private function validateChecksum(string $nip): bool
    {
        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$nip[$i] * $weights[$i];
        }

        $checksum = $sum % 11;
        if ($checksum === 10) {
            return false;
        }

        return $checksum === (int)$nip[9];
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
