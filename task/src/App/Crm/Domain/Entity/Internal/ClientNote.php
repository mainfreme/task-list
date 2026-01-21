<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\ValueObject\NoteType;

/**
 * @internal
 */
final class ClientNote
{
    private ?string $id = null;

    public function __construct(
        private string $clientUuid,
        private string $userUuid,
        private string $content,
        private NoteType $type = NoteType::NOTE,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $clientUuid,
        string $userUuid,
        string $content,
        NoteType $type = NoteType::NOTE
    ): self {
        return new self($clientUuid, $userUuid, $content, $type);
    }

    public static function fromDatabase(
        string $clientUuid,
        string $userUuid,
        string $content,
        NoteType $type,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($clientUuid, $userUuid, $content, $type, $createdAt, $updatedAt);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getClientUuid(): string
    {
        return $this->clientUuid;
    }

    public function setClientUuid(string $clientUuid): void
    {
        $this->clientUuid = $clientUuid;
        $this->touch();
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function setUserUuid(string $userUuid): void
    {
        $this->userUuid = $userUuid;
        $this->touch();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->touch();
    }

    public function getType(): NoteType
    {
        return $this->type;
    }

    public function setType(NoteType $type): void
    {
        $this->type = $type;
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
