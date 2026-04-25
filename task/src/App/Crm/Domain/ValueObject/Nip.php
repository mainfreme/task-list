<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AbstractValueObject;
use App\Shared\Domain\ValueObject\ValueObjectInterface;

final class Nip extends AbstractValueObject implements ValueObjectInterface
{
    /** Treść błędu HTTP — używaj w FormRequest / controllerze, nie w VO. */
    public const INVALID_MESSAGE = 'Invalid NIP format. NIP must contain 10 valid digits and a correct checksum.';

    private function __construct(
        string $value
    ) {
        parent::__construct($value);
    }

    public static function tryFrom(string $nip): ?self
    {
        if (!self::isValid($nip)) {
            return null;
        }

        return new self($nip);
    }

    public static function isValid(string $value): bool
    {
        if (trim($value) === '') {
            return false;
        }

        $cleaned = preg_replace('/[\s-]/', '', $value);

        if (!preg_match('/^\d{10}$/', $cleaned)) {
            return false;
        }

        return self::validateChecksum($cleaned);
    }

    private static function validateChecksum(string $nip): bool
    {
        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $nip[$i] * $weights[$i];
        }

        $checksum = $sum % 11;
        if ($checksum === 10) {
            return false;
        }

        return $checksum === (int) $nip[9];
    }

    public function validate(): void
    {
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
