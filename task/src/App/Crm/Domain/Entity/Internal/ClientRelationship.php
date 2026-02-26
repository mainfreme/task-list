<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\RelationshipType;
use App\Crm\Domain\ValueObject\RelationshipNotes;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * @internal
 */
final class ClientRelationship
{
    private ?Uuid $id = null;

    public function __construct(
        private Uuid $parentUuid,
        private Uuid $childUuid,
        private RelationshipType $relationshipType,
        private ?RelationshipNotes $notes = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        Uuid $parentUuid,
        Uuid $childUuid,
        RelationshipType $relationshipType,
        ?RelationshipNotes $notes = null
    ): self {
        return new self($parentUuid, $childUuid, $relationshipType, $notes);
    }

    public static function fromDatabase(
        Uuid $parentUuid,
        Uuid $childUuid,
        RelationshipType $relationshipType,
        ?RelationshipNotes $notes = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($parentUuid, $childUuid, $relationshipType, $notes, $createdAt, $updatedAt);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getParentUuid(): Uuid
    {
        return $this->parentUuid;
    }

    public function setParentUuid(Uuid $parentUuid): void
    {
        $this->parentUuid = $parentUuid;
        $this->touch();
    }

    public function getChildUuid(): Uuid
    {
        return $this->childUuid;
    }

    public function setChildUuid(Uuid $childUuid): void
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
