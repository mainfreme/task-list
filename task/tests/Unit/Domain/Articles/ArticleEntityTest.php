<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Articles;

use App\Articles\Domain\Entity\Article;
use App\Articles\Domain\Enums\ArticleStatus;
use App\Shared\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

final class ArticleEntityTest extends TestCase
{
    private const SAMPLE_UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_create_sets_draft_by_default_and_nulls_optional_business_fields(): void
    {
        $article = Article::create(
            'Tytuł',
            'tytul-slug',
            null,
            '<p>Treść</p>',
            'aktualności',
            'Jan K.',
        );

        $this->assertSame(ArticleStatus::DRAFT, $article->getStatus());
        $this->assertNull($article->getExcerpt());
        $this->assertNull($article->getApplicationManagerId());
        $this->assertNull($article->getPublishedAt());
        $this->assertNull($article->getMetaTitle());
        $this->assertNull($article->getMetaDescription());
        $this->assertNull($article->getCanonicalUrl());
        $this->assertNull($article->getOgTitle());
        $this->assertNull($article->getOgDescription());
        $this->assertNull($article->getOgImageUrl());
        $this->assertNull($article->getRobots());
    }

    public function test_create_accepts_explicit_status_and_application_manager_id(): void
    {
        $appId = Uuid::fromString(self::SAMPLE_UUID);

        $article = Article::create(
            'Tytuł',
            'slug',
            'Lead',
            'Body',
            'blog',
            'Anna',
            $appId,
            ArticleStatus::REVIEW
        );

        $this->assertSame(ArticleStatus::REVIEW, $article->getStatus());
        $this->assertTrue($appId->equals($article->getApplicationManagerId()));
    }

    public function test_set_id_stores_uuid(): void
    {
        $article = $this->createMinimalArticle();
        $id = Uuid::fromString(self::SAMPLE_UUID);

        $article->setId($id);

        $this->assertTrue($id->equals($article->getId()));
    }

    public function test_from_database_preserves_core_fields_status_and_timestamps(): void
    {
        $publishedAt = new \DateTimeImmutable('2025-06-15 14:30:00');
        $createdAt = new \DateTimeImmutable('2025-06-01 09:00:00');
        $updatedAt = new \DateTimeImmutable('2025-06-10 11:00:00');

        $article = Article::fromDatabase(
            'Z bazy',
            'z-bazy',
            null,
            'Treść',
            ArticleStatus::PUBLISHED,
            'poradnik',
            null,
            'Redakcja',
            $publishedAt,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $createdAt,
            $updatedAt
        );

        $this->assertSame('Z bazy', $article->getTitle());
        $this->assertSame('z-bazy', $article->getSlug());
        $this->assertSame(ArticleStatus::PUBLISHED, $article->getStatus());
        $this->assertSame('poradnik', $article->getCategory());
        $this->assertSame('Redakcja', $article->getAuthor());
        $this->assertSame(
            $publishedAt->format(DATE_ATOM),
            $article->getPublishedAt()?->format(DATE_ATOM)
        );
        $this->assertSame(
            $createdAt->format(DATE_ATOM),
            $article->getCreatedAt()->format(DATE_ATOM)
        );
        $this->assertSame(
            $updatedAt->format(DATE_ATOM),
            $article->getUpdatedAt()->format(DATE_ATOM)
        );
    }

    public function test_from_database_maps_all_seo_fields_when_set(): void
    {
        $article = Article::fromDatabase(
            'T',
            't',
            null,
            'b',
            ArticleStatus::DRAFT,
            'c',
            null,
            'a',
            null,
            'Meta title',
            'Meta opis',
            'https://example.com/a',
            'OG title',
            'OG opis',
            'https://example.com/og.png',
            'noindex,nofollow',
            new \DateTimeImmutable('2020-01-01 00:00:00'),
            new \DateTimeImmutable('2020-01-02 00:00:00')
        );

        $this->assertSame('Meta title', $article->getMetaTitle());
        $this->assertSame('Meta opis', $article->getMetaDescription());
        $this->assertSame('https://example.com/a', $article->getCanonicalUrl());
        $this->assertSame('OG title', $article->getOgTitle());
        $this->assertSame('OG opis', $article->getOgDescription());
        $this->assertSame('https://example.com/og.png', $article->getOgImageUrl());
        $this->assertSame('noindex,nofollow', $article->getRobots());
    }

    public function test_set_title_changes_title(): void
    {
        $article = $this->createMinimalArticle();

        $article->setTitle('Nowy tytuł');

        $this->assertSame('Nowy tytuł', $article->getTitle());
    }

    public function test_set_title_advances_updated_at(): void
    {
        $article = $this->createMinimalArticle();
        $before = $article->getUpdatedAt();
        sleep(1);
        $article->setTitle('Inny');

        $this->assertGreaterThan(
            $before->getTimestamp(),
            $article->getUpdatedAt()->getTimestamp()
        );
    }

    private function createMinimalArticle(): Article
    {
        return Article::create(
            'Tytuł',
            'slug',
            null,
            'Body',
            'kat',
            'Autor'
        );
    }
}
