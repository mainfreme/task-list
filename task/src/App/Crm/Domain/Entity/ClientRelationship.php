<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\Crm\Domain\ValueObject\RelationshipType;

final class ClientRelationship
{
    private ?string $id = null;

    public function __construct(
        private string $parentUuid,
        private string $childUuid,
        private RelationshipType $relationshipType,
        private ?string $notes = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $parentUuid,
        string $childUuid,
        RelationshipType $relationshipType,
        ?string $notes = null
    ): self {
        return new self($parentUuid, $childUuid, $relationshipType, $notes);
    }

    public static function fromDatabase(
        string $parentUuid,
        string $childUuid,
        RelationshipType $relationshipType,
        ?string $notes = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($parentUuid, $childUuid, $relationshipType, $notes, $createdAt, $updatedAt);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getParentUuid(): string
    {
        return $this->parentUuid;
    }

    public function setParentUuid(string $parentUuid): void
    {
        $this->parentUuid = $parentUuid;
        $this->touch();
    }

    public function getChildUuid(): string
    {
        return $this->childUuid;
    }

    public function setChildUuid(string $childUuid): void
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
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
