<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\NoteType;
use App\Crm\Domain\ValueObject\NoteContent;
use App\Crm\Domain\ValueObject\Uuid\NoteId;
use App\Crm\Domain\ValueObject\Uuid\ClientId;
use App\Crm\Domain\ValueObject\Uuid\UserId;

/**
 * @internal
 */
final class ClientNote
{
    private ?NoteId $id = null;

    public function __construct(
        private ClientId $clientUuid,
        private UserId $userUuid,
        private NoteContent $content,
        private NoteType $type = NoteType::NOTE,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ClientId $clientUuid,
        UserId $userUuid,
        NoteContent $content,
        NoteType $type = NoteType::NOTE
    ): self {
        return new self($clientUuid, $userUuid, $content, $type);
    }

    public static function fromDatabase(
        ClientId $clientUuid,
        UserId $userUuid,
        NoteContent $content,
        NoteType $type,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($clientUuid, $userUuid, $content, $type, $createdAt, $updatedAt);
    }

    public function getId(): ?NoteId
    {
        return $this->id;
    }

    public function setId(NoteId $id): void
    {
        $this->id = $id;
    }

    public function getClientUuid(): ClientId
    {
        return $this->clientUuid;
    }

    public function setClientUuid(ClientId $clientUuid): void
    {
        $this->clientUuid = $clientUuid;
        $this->touch();
    }

    public function getUserUuid(): UserId
    {
        return $this->userUuid;
    }

    public function setUserUuid(UserId $userUuid): void
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
