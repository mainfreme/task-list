<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

enum ContactRole: string
{
    case BILLING = 'billing';
    case TECHNICAL = 'technical';
    case ADMIN = 'admin';
    case SALES = 'sales';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BILLING => 'Billing',
            self::TECHNICAL => 'Technical',
            self::ADMIN => 'Admin',
            self::SALES => 'Sales',
            self::OTHER => 'Other',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
