<?php

declare(strict_types=1);

namespace App\Crm\Domain\Enums;

enum ContactType: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case MOBILE = 'mobile';
    case FAX = 'fax';
    case WEBSITE = 'website';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::PHONE => 'Phone',
            self::MOBILE => 'Mobile',
            self::FAX => 'Fax',
            self::WEBSITE => 'Website',
            self::OTHER => 'Other',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
