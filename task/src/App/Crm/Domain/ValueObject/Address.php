<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

use InvalidArgumentException;
use App\Crm\Domain\Enums\AddressType;
use App\Shared\Domain\ValueObject\AbstractValueObject;

final class Address extends AbstractValueObject
{
    private const MAX_STREET_LENGTH = 255;
    private const MAX_POSTAL_CODE_LENGTH = 20;
    private const MAX_CITY_LENGTH = 100;
    private const MAX_STATE_PROVINCE_LENGTH = 100;
    private const MAX_COUNTRY_LENGTH = 100;
    private const MAX_HOUSE_NUMBER_LENGTH = 10;
    private const MAX_APARTMENT_NUMBER_LENGTH = 15;
    
    private const MIN_LATITUDE = -90.0;
    private const MAX_LATITUDE = 90.0;
    private const MIN_LONGITUDE = -180.0;
    private const MAX_LONGITUDE = 180.0;

    public function __construct(
        private readonly string $street,
        private readonly string $postalCode,
        private readonly string $city,
        private readonly string $stateProvince,
        private readonly string $country,
        private readonly string $houseNumber,
        private readonly string $apartmentNumber,
        private readonly string $type = 'other',
        private readonly string $additionalInfo = '',
        private readonly bool $isPrimary = false,
        private readonly bool $isActive = true,
        private readonly ?float $latitude = null,
        private readonly ?float $longitude = null
    ) {
        $this->validate();
    }

    public function getValue(): string
    {
        return sprintf(
            '%s %s %s %s %s %s',
            $this->street,
            $this->houseNumber,
            $this->apartmentNumber ? 'm. ' . $this->apartmentNumber : null,
            $this->postalCode . ' ' . $this->city,
            $this->stateProvince,
            $this->country
        );
    }

    public function validate(): void
    {
        $this->validateStreet();
        $this->validatePostalCode();
        $this->validateCity();
        $this->validateStateProvince();
        $this->validateCountry();
        $this->validateHouseNumber();
        $this->validateApartmentNumber();
        $this->validateType();
        $this->validateCoordinates();
    }

    private function validateStreet(): void
    {
        if (empty(trim($this->street))) {
            throw new InvalidArgumentException('Street cannot be empty');
        }

        if (strlen($this->street) > self::MAX_STREET_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Street cannot be longer than %d characters', self::MAX_STREET_LENGTH)
            );
        }
    }

    private function validatePostalCode(): void
    {
        if (empty(trim($this->postalCode))) {
            throw new InvalidArgumentException('Postal code cannot be empty');
        }

        if (strlen($this->postalCode) > self::MAX_POSTAL_CODE_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Postal code cannot be longer than %d characters', self::MAX_POSTAL_CODE_LENGTH)
            );
        }
    }

    private function validateCity(): void
    {
        if (empty(trim($this->city))) {
            throw new InvalidArgumentException('City cannot be empty');
        }

        if (strlen($this->city) > self::MAX_CITY_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('City cannot be longer than %d characters', self::MAX_CITY_LENGTH)
            );
        }
    }

    private function validateStateProvince(): void
    {
        if (empty(trim($this->stateProvince))) {
            throw new InvalidArgumentException('State/Province cannot be empty');
        }

        if (strlen($this->stateProvince) > self::MAX_STATE_PROVINCE_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('State/Province cannot be longer than %d characters', self::MAX_STATE_PROVINCE_LENGTH)
            );
        }
    }

    private function validateCountry(): void
    {
        if (empty(trim($this->country))) {
            throw new InvalidArgumentException('Country cannot be empty');
        }

        if (strlen($this->country) > self::MAX_COUNTRY_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Country cannot be longer than %d characters', self::MAX_COUNTRY_LENGTH)
            );
        }
    }

    private function validateHouseNumber(): void
    {
        if (empty(trim($this->houseNumber))) {
            throw new InvalidArgumentException('House number cannot be empty');
        }

        if (strlen($this->houseNumber) > self::MAX_HOUSE_NUMBER_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('House number cannot be longer than %d characters', self::MAX_HOUSE_NUMBER_LENGTH)
            );
        }
    }

    private function validateApartmentNumber(): void
    {
        if (strlen($this->apartmentNumber) > self::MAX_APARTMENT_NUMBER_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Apartment number cannot be longer than %d characters', self::MAX_APARTMENT_NUMBER_LENGTH)
            );
        }
    }

    private function validateType(): void
    {
        if (!in_array($this->type, AddressType::cases(), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid address type. Allowed values: %s',
                    implode(', ', AddressType::cases())
                )
            );
        }
    }

    private function validateCoordinates(): void
    {
        if ($this->latitude !== null) {
            if ($this->latitude < self::MIN_LATITUDE || $this->latitude > self::MAX_LATITUDE) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Latitude must be between %f and %f',
                        self::MIN_LATITUDE,
                        self::MAX_LATITUDE
                    )
                );
            }
        }

        if ($this->longitude !== null) {
            if ($this->longitude < self::MIN_LONGITUDE || $this->longitude > self::MAX_LONGITUDE) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Longitude must be between %f and %f',
                        self::MIN_LONGITUDE,
                        self::MAX_LONGITUDE
                    )
                );
            }
        }

        // If one coordinate is provided, the other should also be provided
        if (($this->latitude !== null && $this->longitude === null) ||
            ($this->latitude === null && $this->longitude !== null)) {
            throw new InvalidArgumentException('Both latitude and longitude must be provided together, or both must be null');
        }
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStateProvince(): string
    {
        return $this->stateProvince;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function getApartmentNumber(): string
    {
        return $this->apartmentNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function equals(Address $other): bool
    {
        return $this->street === $other->street
            && $this->postalCode === $other->postalCode
            && $this->city === $other->city
            && $this->stateProvince === $other->stateProvince
            && $this->country === $other->country
            && $this->houseNumber === $other->houseNumber
            && $this->apartmentNumber === $other->apartmentNumber
            && $this->type === $other->type
            && $this->additionalInfo === $other->additionalInfo
            && $this->isPrimary === $other->isPrimary
            && $this->isActive === $other->isActive
            && $this->latitude === $other->latitude
            && $this->longitude === $other->longitude;
    }
}
