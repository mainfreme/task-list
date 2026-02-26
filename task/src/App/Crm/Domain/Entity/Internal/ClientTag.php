<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\ValueObject\TagColor;
use App\Crm\Domain\ValueObject\TagDescription;
use App\Crm\Domain\ValueObject\TagName;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * @internal
 */
final class ClientTag
{
    private ?Uuid $id = null;

    public function __construct(
        private TagName $name,
        private ?TagColor $color = null,
        private ?TagDescription $description = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        TagName $name,
        ?TagColor $color = null,
        ?TagDescription $description = null
    ): self {
        return new self($name, $color, $description);
    }

    public static function fromDatabase(
        TagName $name,
        ?TagColor $color = null,
        ?TagDescription $description = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self($name, $color, $description, $createdAt, $updatedAt);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getName(): TagName
    {
        return $this->name;
    }

    public function setName(TagName $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function getColor(): ?TagColor
    {
        return $this->color;
    }

    public function setColor(?TagColor $color): void
    {
        $this->color = $color;
        $this->touch();
    }

    public function getDescription(): ?TagDescription
    {
        return $this->description;
    }

    public function setDescription(?TagDescription $description): void
    {
        $this->description = $description;
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
