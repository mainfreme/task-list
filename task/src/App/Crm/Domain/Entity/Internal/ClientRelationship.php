<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\RelationshipType;
use App\Crm\Domain\ValueObject\RelationshipNotes;
use App\Crm\Domain\ValueObject\Uuid\RelationshipId;
use App\Crm\Domain\ValueObject\Uuid\ClientId;

/**
 * @internal
 */
final class ClientRelationship
{
    private ?RelationshipId $id = null;

    public function __construct(
        private ClientId $parentUuid,
        private ClientId $childUuid,
        private RelationshipType $relationshipType,
        private ?RelationshipNotes $notes = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ClientId $parentUuid,
        ClientId $childUuid,
        RelationshipType $relationshipType,
        ?RelationshipNotes $notes = null
    ): self {
        return new self($parentUuid, $childUuid, $relationshipType, $notes);
    }

    public static function fromDatabase(
        ClientId $parentUuid,
        ClientId $childUuid,
        RelationshipType $relationshipType,
        ?RelationshipNotes $notes = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($parentUuid, $childUuid, $relationshipType, $notes, $createdAt, $updatedAt);
    }

    public function getId(): ?RelationshipId
    {
        return $this->id;
    }

    public function setId(RelationshipId $id): void
    {
        $this->id = $id;
    }

    public function getParentUuid(): ClientId
    {
        return $this->parentUuid;
    }

    public function setParentUuid(ClientId $parentUuid): void
    {
        $this->parentUuid = $parentUuid;
        $this->touch();
    }

    public function getChildUuid(): ClientId
    {
        return $this->childUuid;
    }

    public function setChildUuid(ClientId $childUuid): void
    {
        $this->childUuid = $childUuid;
        $this->touch();
    }

    public function getRelationshipType(): RelationshipType
    {
        return $this->relationshipType;
    }

    public function setRelationshipType(RelationshipType $relationshipType): void
    {
        $this->relationshipType = $relationshipType;
        $this->touch();
    }

    public function getNotes(): ?RelationshipNotes
    {
        return $this->notes;
    }

    public function setNotes(?RelationshipNotes $notes): void
    {
        $this->notes = $notes;
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
