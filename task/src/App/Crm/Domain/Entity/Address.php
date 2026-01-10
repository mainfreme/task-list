<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\Crm\Domain\ValueObject\AddressType;

final class Address
{
    private ?string $id = null;

    public function __construct(
        private string $clientUuid,
        private string $street,
        private string $postalCode,
        private string $city,
        private string $stateProvince,
        private string $country,
        private string $additionalInfo,
        private string $houseNumber,
        private string $apartmentNumber,
        private AddressType $type = AddressType::OTHER,
        private bool $isPrimary = false,
        private bool $isActive = true,
        private ?float $latitude = null,
        private ?float $longitude = null,
        private ?\DateTimeImmutable $addedAt = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->addedAt = $this->addedAt ?? new \DateTimeImmutable();
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $clientUuid,
        string $street,
        string $postalCode,
        string $city,
        string $stateProvince,
        string $country,
        string $additionalInfo,
        string $houseNumber,
        string $apartmentNumber,
        AddressType $type = AddressType::OTHER,
        bool $isPrimary = false
    ): self {
        return new self(
            $clientUuid,
            $street,
            $postalCode,
            $city,
            $stateProvince,
            $country,
            $additionalInfo,
            $houseNumber,
            $apartmentNumber,
            $type,
            $isPrimary
        );
    }

    public static function fromDatabase(
        string $clientUuid,
        string $street,
        string $postalCode,
        string $city,
        string $stateProvince,
        string $country,
        string $additionalInfo,
        string $houseNumber,
        string $apartmentNumber,
        AddressType $type,
        bool $isPrimary = false,
        bool $isActive = true,
        ?float $latitude = null,
        ?float $longitude = null,
        ?\DateTimeImmutable $addedAt = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self(
            $clientUuid,
            $street,
            $postalCode,
            $city,
            $stateProvince,
            $country,
            $additionalInfo,
            $houseNumber,
            $apartmentNumber,
            $type,
            $isPrimary,
            $isActive,
            $latitude,
            $longitude,
            $addedAt,
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

    public function getClientUuid(): string
    {
        return $this->clientUuid;
    }

    public function setClientUuid(string $clientUuid): void
    {
        $this->clientUuid = $clientUuid;
        $this->touch();
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
        $this->touch();
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
        $this->touch();
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
        $this->touch();
    }

    public function getStateProvince(): string
    {
        return $this->stateProvince;
    }

    public function setStateProvince(string $stateProvince): void
    {
        $this->stateProvince = $stateProvince;
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

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(string $additionalInfo): void
    {
        $this->additionalInfo = $additionalInfo;
        $this->touch();
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
        $this->touch();
    }

    public function getApartmentNumber(): string
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(string $apartmentNumber): void
    {
        $this->apartmentNumber = $apartmentNumber;
        $this->touch();
    }

    public function getType(): AddressType
    {
        return $this->type;
    }

    public function setType(AddressType $type): void
    {
        $this->type = $type;
        $this->touch();
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
        $this->touch();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->touch();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->touch();
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
        $this->touch();
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
        $this->touch();
    }

    public function getAddedAt(): \DateTimeImmutable
    {
        return $this->addedAt;
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
