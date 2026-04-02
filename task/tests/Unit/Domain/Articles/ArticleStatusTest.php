<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Articles;

use App\Articles\Domain\Enums\ArticleStatus;
use PHPUnit\Framework\TestCase;
use ValueError;

final class ArticleStatusTest extends TestCase
{
    public function test_from_string_throws_on_invalid_value(): void
    {
        $this->expectException(ValueError::class);

        ArticleStatus::fromString('invalid_status');
    }

    public function test_from_string_accepts_all_valid_statuses(): void
    {
        $this->assertSame(ArticleStatus::DRAFT, ArticleStatus::fromString('draft'));
        $this->assertSame(ArticleStatus::REVIEW, ArticleStatus::fromString('review'));
        $this->assertSame(ArticleStatus::PUBLISHED, ArticleStatus::fromString('published'));
        $this->assertSame(ArticleStatus::ARCHIVED, ArticleStatus::fromString('archived'));
    }

    /** Wartości backed enum muszą odpowiadać stringom w DB – zmiana w enumie nie umknie */
    public function test_backed_values_match_database_strings(): void
    {
        $this->assertSame('draft', ArticleStatus::DRAFT->value);
        $this->assertSame('review', ArticleStatus::REVIEW->value);
        $this->assertSame('published', ArticleStatus::PUBLISHED->value);
        $this->assertSame('archived', ArticleStatus::ARCHIVED->value);
    }
}
