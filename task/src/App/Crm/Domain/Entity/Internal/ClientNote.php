<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\NoteType;
use App\Crm\Domain\ValueObject\NoteContent;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * @internal
 */
final class ClientNote
{
    private ?Uuid $id = null;

    public function __construct(
        private Uuid $clientUuid,
        private Uuid $userUuid,
        private NoteContent $content,
        private NoteType $type = NoteType::NOTE,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        Uuid $clientUuid,
        Uuid $userUuid,
        NoteContent $content,
        NoteType $type = NoteType::NOTE
    ): self {
        return new self($clientUuid, $userUuid, $content, $type);
    }

    public static function fromDatabase(
        Uuid $clientUuid,
        Uuid $userUuid,
        NoteContent $content,
        NoteType $type,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($clientUuid, $userUuid, $content, $type, $createdAt, $updatedAt);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getClientUuid(): Uuid
    {
        return $this->clientUuid;
    }

    public function setClientUuid(Uuid $clientUuid): void
    {
        $this->clientUuid = $clientUuid;
        $this->touch();
    }

    public function getUserUuid(): Uuid
    {
        return $this->userUuid;
    }

    public function setUserUuid(Uuid $userUuid): void
    {
        $this->userUuid = $userUuid;
        $this->touch();
    }

    public function getContent(): NoteContent
    {
        return $this->content;
    }

    public function setContent(NoteContent $content): void
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
