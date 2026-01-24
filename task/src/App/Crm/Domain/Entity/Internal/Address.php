<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity\Internal;

use App\Crm\Domain\Enums\AddressType;
use App\Crm\Domain\ValueObject\Street;
use App\Crm\Domain\ValueObject\PostalCode;
use App\Crm\Domain\ValueObject\City;
use App\Crm\Domain\ValueObject\StateProvince;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\AdditionalInfo;
use App\Crm\Domain\ValueObject\HouseNumber;
use App\Crm\Domain\ValueObject\ApartmentNumber;
use App\Crm\Domain\ValueObject\IsPrimary;
use App\Crm\Domain\ValueObject\IsActive;
use App\Crm\Domain\ValueObject\Latitude;
use App\Crm\Domain\ValueObject\Longitude;
use App\Crm\Domain\ValueObject\Uuid\AddressId;
use App\Crm\Domain\ValueObject\Uuid\ClientId;

/**
 * @internal
 */
final class Address
{
    private ?AddressId $id = null;

    public function __construct(
        private ClientId $clientUuid,
        private Street $street,
        private PostalCode $postalCode,
        private City $city,
        private StateProvince $stateProvince,
        private Country $country,
        private AdditionalInfo $additionalInfo,
        private HouseNumber $houseNumber,
        private ApartmentNumber $apartmentNumber,
        private AddressType $type = AddressType::OTHER,
        private IsPrimary $isPrimary = new IsPrimary(false),
        private IsActive $isActive = new IsActive(true),
        private ?Latitude $latitude = null,
        private ?Longitude $longitude = null,
        private ?\DateTimeImmutable $addedAt = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->addedAt = $this->addedAt ?? new \DateTimeImmutable();
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        ClientId $clientUuid,
        Street $street,
        PostalCode $postalCode,
        City $city,
        StateProvince $stateProvince,
        Country $country,
        AdditionalInfo $additionalInfo,
        HouseNumber $houseNumber,
        ApartmentNumber $apartmentNumber,
        AddressType $type = AddressType::OTHER,
        IsPrimary $isPrimary = new IsPrimary(false)
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
        ClientId $clientUuid,
        Street $street,
        PostalCode $postalCode,
        City $city,
        StateProvince $stateProvince,
        Country $country,
        AdditionalInfo $additionalInfo,
        HouseNumber $houseNumber,
        ApartmentNumber $apartmentNumber,
        AddressType $type,
        IsPrimary $isPrimary,
        IsActive $isActive,
        ?Latitude $latitude = null,
        ?Longitude $longitude = null,
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

    public function getId(): ?AddressId
    {
        return $this->id;
    }

    public function setId(AddressId $id): void
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

    public function getStreet(): Street
    {
        return $this->street;
    }

    public function setStreet(Street $street): void
    {
        $this->street = $street;
        $this->touch();
    }

    public function getPostalCode(): PostalCode
    {
        return $this->postalCode;
    }

    public function setPostalCode(PostalCode $postalCode): void
    {
        $this->postalCode = $postalCode;
        $this->touch();
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): void
    {
        $this->city = $city;
        $this->touch();
    }

    public function getStateProvince(): StateProvince
    {
        return $this->stateProvince;
    }

    public function setStateProvince(StateProvince $stateProvince): void
    {
        $this->stateProvince = $stateProvince;
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

    public function getAdditionalInfo(): AdditionalInfo
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(AdditionalInfo $additionalInfo): void
    {
        $this->additionalInfo = $additionalInfo;
        $this->touch();
    }

    public function getHouseNumber(): HouseNumber
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(HouseNumber $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
        $this->touch();
    }

    public function getApartmentNumber(): ApartmentNumber
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(ApartmentNumber $apartmentNumber): void
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

    public function isPrimary(): IsPrimary
    {
        return $this->isPrimary;
    }

    public function setPrimary(IsPrimary $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
        $this->touch();
    }

    public function isActive(): IsActive
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = IsActive::fromBool(true);
        $this->touch();
    }

    public function deactivate(): void
    {
        $this->isActive = IsActive::fromBool(false);
        $this->touch();
    }

    public function getLatitude(): ?Latitude
    {
        return $this->latitude;
    }

    public function setLatitude(?Latitude $latitude): void
    {
        $this->latitude = $latitude;
        $this->touch();
    }

    public function getLongitude(): ?Longitude
    {
        return $this->longitude;
    }

    public function setLongitude(?Longitude $longitude): void
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
