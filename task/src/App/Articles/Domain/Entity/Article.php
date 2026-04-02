<?php

declare(strict_types=1);

namespace App\Articles\Domain\Entity;

use App\Articles\Domain\Enums\ArticleStatus;
use App\Shared\Domain\ValueObject\Uuid;

final class Article
{
    private ?Uuid $id = null;

    public function __construct(
        private string $title,
        private string $slug,
        private ?string $excerpt,
        private string $body,
        private ArticleStatus $status,
        private string $category,
        private ?Uuid $applicationManagerId,
        private string $author,
        private ?\DateTimeImmutable $publishedAt,
        private ?string $metaTitle,
        private ?string $metaDescription,
        private ?string $canonicalUrl,
        private ?string $ogTitle,
        private ?string $ogDescription,
        private ?string $ogImageUrl,
        private ?string $robots,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $title,
        string $slug,
        ?string $excerpt,
        string $body,
        string $category,
        string $author,
        ?Uuid $applicationManagerId = null,
        ArticleStatus $status = ArticleStatus::DRAFT
    ): self {
        return new self(
            $title,
            $slug,
            $excerpt,
            $body,
            $status,
            $category,
            $applicationManagerId,
            $author,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }

    public static function fromDatabase(
        string $title,
        string $slug,
        ?string $excerpt,
        string $body,
        ArticleStatus $status,
        string $category,
        ?Uuid $applicationManagerId,
        string $author,
        ?\DateTimeImmutable $publishedAt,
        ?string $metaTitle,
        ?string $metaDescription,
        ?string $canonicalUrl,
        ?string $ogTitle,
        ?string $ogDescription,
        ?string $ogImageUrl,
        ?string $robots,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self(
            $title,
            $slug,
            $excerpt,
            $body,
            $status,
            $category,
            $applicationManagerId,
            $author,
            $publishedAt,
            $metaTitle,
            $metaDescription,
            $canonicalUrl,
            $ogTitle,
            $ogDescription,
            $ogImageUrl,
            $robots,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->touch();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
        $this->touch();
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): void
    {
        $this->excerpt = $excerpt;
        $this->touch();
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
        $this->touch();
    }

    public function getStatus(): ArticleStatus
    {
        return $this->status;
    }

    public function setStatus(ArticleStatus $status): void
    {
        $this->status = $status;
        $this->touch();
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->touch();
    }

    public function getApplicationManagerId(): ?Uuid
    {
        return $this->applicationManagerId;
    }

    public function setApplicationManagerId(?Uuid $applicationManagerId): void
    {
        $this->applicationManagerId = $applicationManagerId;
        $this->touch();
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
        $this->touch();
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
        $this->touch();
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
        $this->touch();
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
        $this->touch();
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->canonicalUrl;
    }

    public function setCanonicalUrl(?string $canonicalUrl): void
    {
        $this->canonicalUrl = $canonicalUrl;
        $this->touch();
    }

    public function getOgTitle(): ?string
    {
        return $this->ogTitle;
    }

    public function setOgTitle(?string $ogTitle): void
    {
        $this->ogTitle = $ogTitle;
        $this->touch();
    }

    public function getOgDescription(): ?string
    {
        return $this->ogDescription;
    }

    public function setOgDescription(?string $ogDescription): void
    {
        $this->ogDescription = $ogDescription;
        $this->touch();
    }

    public function getOgImageUrl(): ?string
    {
        return $this->ogImageUrl;
    }

    public function setOgImageUrl(?string $ogImageUrl): void
    {
        $this->ogImageUrl = $ogImageUrl;
        $this->touch();
    }

    public function getRobots(): ?string
    {
        return $this->robots;
    }

    public function setRobots(?string $robots): void
    {
        $this->robots = $robots;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
