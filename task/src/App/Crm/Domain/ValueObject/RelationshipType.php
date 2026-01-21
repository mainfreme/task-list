<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

enum RelationshipType: string
{
    case PARENT_COMPANY = 'parent_company';
    case SUBSIDIARY = 'subsidiary';
    case PARTNER = 'partner';
    case COMPETITOR = 'competitor';

    public function label(): string
    {
        return match ($this) {
            self::PARENT_COMPANY => 'Parent Company',
            self::SUBSIDIARY => 'Subsidiary',
            self::PARTNER => 'Partner',
            self::COMPETITOR => 'Competitor',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
