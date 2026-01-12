<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\ValueObject\ClientStatus;

/**
 * @internal
 */
final class Client
{
    private ?string $id = null;

    public function __construct(
        private string $name,
        private string $nip,
        private string $country,
        private ClientStatus $status = ClientStatus::LEAD,
        private ?string $addressUuid = null,
        private ?string $regon = null,
        private ?string $pesel = null,
        private ?string $source = null,
        private ?int $rating = null,
        private ?\DateTimeImmutable $lastContactedAt = null,
        private ?\DateTimeImmutable $nextContactAt = null,
        private ?string $notes = null,
        private bool $isDelete = false,
        private bool $isCompany = false,
        private ?\DateTimeImmutable $deletedAt = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $name,
        string $nip,
        string $country,
        bool $isCompany = false,
        ?string $regon = null,
        ?string $pesel = null,
        ?string $addressUuid = null
    ): self {
        return new self(
            $name,
            $nip,
            $country,
            ClientStatus::LEAD,
            $addressUuid,
            $regon,
            $pesel,
            null,
            null,
            null,
            null,
            null,
            false,
            $isCompany
        );
    }

    public static function fromDatabase(
        string $name,
        string $nip,
        string $country,
        ClientStatus $status,
        bool $isCompany = false,
        ?string $addressUuid = null,
        ?string $regon = null,
        ?string $pesel = null,
        ?string $source = null,
        ?int $rating = null,
        ?\DateTimeImmutable $lastContactedAt = null,
        ?\DateTimeImmutable $nextContactAt = null,
        ?string $notes = null,
        bool $isDelete = false,
        ?\DateTimeImmutable $deletedAt = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self(
            $name,
            $nip,
            $country,
            $status,
            $addressUuid,
            $regon,
            $pesel,
            $source,
            $rating,
            $lastContactedAt,
            $nextContactAt,
            $notes,
            $isDelete,
            $isCompany,
            $deletedAt,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function getNip(): string
    {
        return $this->nip;
    }

    public function setNip(string $nip): void
    {
        $this->nip = $nip;
        $this->touch();
    }

    public function getRegon(): ?string
    {
        return $this->regon;
    }

    public function setRegon(?string $regon): void
    {
        $this->regon = $regon;
        $this->touch();
    }

    public function getPesel(): ?string
    {
        return $this->pesel;
    }

    public function setPesel(?string $pesel): void
    {
        $this->pesel = $pesel;
        $this->touch();
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
        $this->touch();
    }

    public function getStatus(): ClientStatus
    {
        return $this->status;
    }

    public function setStatus(ClientStatus $status): void
    {
        $this->status = $status;
        $this->touch();
    }

    public function getAddressUuid(): ?string
    {
        return $this->addressUuid;
    }

    public function setAddressUuid(?string $addressUuid): void
    {
        $this->addressUuid = $addressUuid;
        $this->touch();
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
        $this->touch();
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
        $this->touch();
    }

    public function getLastContactedAt(): ?\DateTimeImmutable
    {
        return $this->lastContactedAt;
    }

    public function setLastContactedAt(?\DateTimeImmutable $lastContactedAt): void
    {
        $this->lastContactedAt = $lastContactedAt;
        $this->touch();
    }

    public function getNextContactAt(): ?\DateTimeImmutable
    {
        return $this->nextContactAt;
    }

    public function setNextContactAt(?\DateTimeImmutable $nextContactAt): void
    {
        $this->nextContactAt = $nextContactAt;
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

    public function isDelete(): bool
    {
        return $this->isDelete;
    }

    public function setIsDelete(bool $isDelete): void
    {
        $this->isDelete = $isDelete;
        $this->touch();
    }

    public function isCompany(): bool
    {
        return $this->isCompany;
    }

    public function setIsCompany(bool $isCompany): void
    {
        $this->isCompany = $isCompany;
        $this->touch();
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->isDelete = true;
        $this->touch();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
        $this->isDelete = false;
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
