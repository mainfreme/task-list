<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObjectInterface;
use App\Shared\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

final class Iban extends AbstractValueObject implements ValueObjectInterface
{
    private function __construct(
        private readonly string $value
    ) {
        parent::__construct($value);
        $this->validate();
    }

    public static function fromString(string $iban): self
    {
        return new self($iban);
    }

    public function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new InvalidArgumentException('IBAN cannot be empty');
        }

        // Remove spaces for validation
        $cleaned = preg_replace('/\s+/', '', strtoupper($this->value));

        // IBAN format: 15-34 alphanumeric characters, starts with 2 letters (country code)
        if (!preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]{11,30}$/', $cleaned)) {
            throw new InvalidArgumentException('Invalid IBAN format');
        }

        // IBAN checksum validation (mod 97)
        if (!$this->validateChecksum($cleaned)) {
            throw new InvalidArgumentException('Invalid IBAN checksum');
        }
    }

    private function validateChecksum(string $iban): bool
    {
        // Move first 4 characters to end
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Replace letters with numbers (A=10, B=11, ..., Z=35)
        $numeric = '';
        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (ctype_alpha($char)) {
                $numeric .= (ord($char) - ord('A') + 10);
            } else {
                $numeric .= $char;
            }
        }

        // Calculate mod 97
        $remainder = '';
        for ($i = 0; $i < strlen($numeric); $i++) {
            $remainder = ($remainder . $numeric[$i]) % 97;
        }

        return $remainder === 1;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return strtoupper(preg_replace('/\s+/', '', $this->value)) === 
               strtoupper(preg_replace('/\s+/', '', $other->getValue()));
    }
}
