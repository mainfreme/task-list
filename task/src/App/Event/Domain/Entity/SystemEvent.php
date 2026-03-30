<?php

declare(strict_types=1);

namespace App\Event\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class SystemEvent
{
    /**
     * @param array<string, mixed>|null $changes
     * @param array<string, mixed>|null $metadata
     */
    private function __construct(
        private Uuid $id,
        private Uuid $userId,
        private ?string $action,
        private ?string $label,
        private ?string $message,
        private ?array $changes,
        private ?string $url,
        private ?string $ipAddress,
        private ?array $metadata,
        private ?DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt,
    ) {
    }

    /**
     * @param array<string, mixed>|null $changes
     * @param array<string, mixed>|null $metadata
     */
    public static function create(
        Uuid $userId,
        ?string $action = null,
        ?string $label = null,
        ?string $message = null,
        ?array $changes = null,
        ?string $url = null,
        ?string $ipAddress = null,
        ?array $metadata = null,
    ): self {
        return new self(
            Uuid::generate(),
            $userId,
            $action,
            $label,
            $message,
            $changes,
            $url,
            $ipAddress,
            $metadata,
            null,
            null,
            null,
        );
    }

    /**
     * @param array<string, mixed>|null $changes
     * @param array<string, mixed>|null $metadata
     */
    public static function reconstitute(
        Uuid $id,
        Uuid $userId,
        ?string $action,
        ?string $label,
        ?string $message,
        ?array $changes,
        ?string $url,
        ?string $ipAddress,
        ?array $metadata,
        ?DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        ?DateTimeImmutable $deletedAt,
    ): self {
        return new self(
            $id,
            $userId,
            $action,
            $label,
            $message,
            $changes,
            $url,
            $ipAddress,
            $metadata,
            $createdAt,
            $updatedAt,
            $deletedAt,
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getChanges(): ?array
    {
        return $this->changes;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
}
