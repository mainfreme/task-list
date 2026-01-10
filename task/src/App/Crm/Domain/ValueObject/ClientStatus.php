<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

enum ClientStatus: string
{
    case LEAD = 'lead';
    case PROSPECT = 'prospect';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::LEAD => 'Lead',
            self::PROSPECT => 'Prospect',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ARCHIVED => 'Archived',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
