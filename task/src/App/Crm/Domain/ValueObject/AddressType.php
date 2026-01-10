<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

enum AddressType: string
{
    case BILLING = 'billing';
    case SHIPPING = 'shipping';
    case REGISTERED_OFFICE = 'registered_office';
    case DELIVERY = 'delivery';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BILLING => 'Billing',
            self::SHIPPING => 'Shipping',
            self::REGISTERED_OFFICE => 'Registered Office',
            self::DELIVERY => 'Delivery',
            self::OTHER => 'Other',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
