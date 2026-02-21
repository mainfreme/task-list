<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\ClientNotes;
use App\Crm\Domain\ValueObject\ClientRating;
use App\Crm\Domain\ValueObject\ClientSource;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\Nip;
use App\Crm\Domain\ValueObject\Pesel;
use App\Crm\Domain\ValueObject\Regon;
use App\Crm\Domain\ValueObject\Uuid\AddressId;
use App\Crm\Domain\ValueObject\Uuid\ClientId;

/**
 * @internal
 */
final class Client
{
    private ?ClientId $id = null;

    public function __construct(
        private ClientName $name,
        private Nip $nip,
        private Country $country,
        private ClientStatus $status = ClientStatus::LEAD,
        private ?AddressId $addressUuid = null,
        private ?Regon $regon = null,
        private ?Pesel $pesel = null,
        private ?ClientSource $source = null,
        private ?ClientRating $rating = null,
        private ?\DateTimeImmutable $lastContactedAt = null,
        private ?\DateTimeImmutable $nextContactAt = null,
        private ?ClientNotes $notes = null,
        private IsDeleted $isDelete = new IsDeleted(false),
        private IsCompany $isCompany = new IsCompany(false),
        private ?\DateTimeImmutable $deletedAt = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ClientName $name,
        Nip $nip,
        Country $country,
        IsCompany $isCompany,
        ?Regon $regon = null,
        ?Pesel $pesel = null,
        ?AddressId $addressUuid = null
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
            new IsDeleted(false),
            $isCompany
        );
    }

    public static function fromDatabase(
        ClientName $name,
        Nip $nip,
        Country $country,
        ClientStatus $status,
        IsCompany $isCompany,
        ?AddressId $addressUuid = null,
        ?Regon $regon = null,
        ?Pesel $pesel = null,
        ?ClientSource $source = null,
        ?ClientRating $rating = null,
        ?\DateTimeImmutable $lastContactedAt = null,
        ?\DateTimeImmutable $nextContactAt = null,
        ?ClientNotes $notes = null,
        IsDeleted $isDelete,
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

    public function getId(): ?ClientId
    {
        return $this->id;
    }

    public function setId(ClientId $id): void
    {
        $this->id = $id;
    }

    public function getName(): ClientName
    {
        return $this->name;
    }

    public function setName(ClientName $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function getNip(): Nip
    {
        return $this->nip;
    }

    public function setNip(Nip $nip): void
    {
        $this->nip = $nip;
        $this->touch();
    }

    public function getRegon(): ?Regon
    {
        return $this->regon;
    }

    public function setRegon(?Regon $regon): void
    {
        $this->regon = $regon;
        $this->touch();
    }

    public function getPesel(): ?Pesel
    {
        return $this->pesel;
    }

    public function setPesel(?Pesel $pesel): void
    {
        $this->pesel = $pesel;
        $this->touch();
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
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

    public function getAddressUuid(): ?AddressId
    {
        return $this->addressUuid;
    }

    public function setAddressUuid(?AddressId $addressUuid): void
    {
        $this->addressUuid = $addressUuid;
        $this->touch();
    }

    public function getSource(): ?ClientSource
    {
        return $this->source;
    }

    public function setSource(?ClientSource $source): void
    {
        $this->source = $source;
        $this->touch();
    }

    public function getRating(): ?ClientRating
    {
        return $this->rating;
    }

    public function setRating(?ClientRating $rating): void
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

    public function getNotes(): ?ClientNotes
    {
        return $this->notes;
    }

    public function setNotes(?ClientNotes $notes): void
    {
        $this->notes = $notes;
        $this->touch();
    }

    public function isDelete(): IsDeleted
    {
        return $this->isDelete;
    }

    public function setIsDelete(IsDeleted $isDelete): void
    {
        $this->isDelete = $isDelete;
        $this->touch();
    }

    public function isCompany(): IsCompany
    {
        return $this->isCompany;
    }

    public function setIsCompany(IsCompany $isCompany): void
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
        $this->isDelete = IsDeleted::fromBool(true);
        $this->touch();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
        $this->isDelete = IsDeleted::fromBool(false);
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
