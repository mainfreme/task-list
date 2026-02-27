<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;

final class ActivityUserLog
{
    private ?int $id = null;

    private function __construct(
        private ?Uuid $userId,
        private ?string $url,
        private array $logActivity,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ?Uuid $userId,
        ?string $url,
        array $logActivity
    ): self {
        return new self(
            userId: $userId,
            url: $url,
            logActivity: $logActivity
        );
    }

    public static function fromDatabase(
        int $id,
        ?Uuid $userId,
        ?string $url,
        array $logActivity,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        $entity = new self(
            userId: $userId,
            url: $url,
            logActivity: $logActivity,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
        $entity->id = $id;

        return $entity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): ?Uuid
    {
        return $this->userId;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getLogActivity(): array
    {
        return $this->logActivity;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function addActivity(string $key, mixed $value): void
    {
        $this->logActivity[$key] = $value;
        $this->touch();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId?->getValue(),
            'url' => $this->url,
            'log_activity' => $this->logActivity,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
