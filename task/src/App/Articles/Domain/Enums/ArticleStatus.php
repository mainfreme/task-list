<?php

declare(strict_types=1);

namespace App\Articles\Domain\Enums;

enum ArticleStatus: string
{
    case DRAFT = 'draft';
    case REVIEW = 'review';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
