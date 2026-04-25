<?php

declare(strict_types=1);

namespace App\Crm\Application\DTO;

use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\ClientNotes;
use App\Crm\Domain\ValueObject\ClientRating;
use App\Crm\Domain\ValueObject\ClientSource;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\Nip;
use App\Crm\Domain\ValueObject\Pesel;
use App\Crm\Domain\ValueObject\Regon;
use App\Shared\Domain\ValueObject\Uuid;

/**
 * Odpowiedź / widok klienta dla warstwy aplikacji i API (poza domeną).
 */
final readonly class ClientDto
{
    public function __construct(
        public ClientName $name,
        public Nip $nip,
        public Country $country,
        public IsCompany $isCompany,
        public ?Uuid $id = null,
        public ?Regon $regon = null,
        public ?Pesel $pesel = null,
        public ?ClientSource $source = null,
        public ?ClientRating $rating = null,
        public ?ClientNotes $notes = null,
        public ClientStatus $status = ClientStatus::LEAD,
        public ?Uuid $addressUuid = null,
        public ?\DateTimeImmutable $lastContactedAt = null,
        public ?\DateTimeImmutable $nextContactAt = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public static function fromAggregate(CrmClientAggregate $client): self
    {
        return new self(
            name: $client->getName(),
            nip: $client->getNip(),
            country: $client->getCountry(),
            isCompany: $client->getIsCompany(),
            id: $client->getId(),
            regon: $client->getRegon(),
            pesel: $client->getPesel(),
            source: $client->getSource(),
            rating: $client->getRating(),
            notes: $client->getNotes(),
            status: $client->getStatus(),
            addressUuid: $client->getAddressUuid(),
            lastContactedAt: $client->getLastContactedAt(),
            nextContactAt: $client->getNextContactAt(),
            createdAt: $client->getCreatedAt(),
            updatedAt: $client->getUpdatedAt(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id?->getValue(),
            'name' => $this->name->getValue(),
            'nip' => $this->nip->getValue(),
            'country' => $this->country->getValue(),
            'status' => $this->status->value,
            'is_company' => $this->isCompany->toBool(),
            'regon' => $this->regon?->toString(),
            'pesel' => $this->pesel?->toString(),
            'source' => $this->source?->toString(),
            'rating' => $this->rating?->toInt(),
            'notes' => $this->notes?->toString(),
            'address_uuid' => $this->addressUuid?->getValue(),
            'last_contacted_at' => $this->lastContactedAt?->format('Y-m-d H:i:s'),
            'next_contact_at' => $this->nextContactAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Odtwarza DTO z tablicy w kształcie {@see self::toArray()} (np. z cache listy).
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $idRaw = $data['id'] ?? null;

        return new self(
            name: ClientName::fromString($data['name']),
            nip: Nip::fromString($data['nip']),
            country: Country::fromString($data['country']),
            isCompany: IsCompany::fromBool((bool) $data['is_company']),
            id: is_string($idRaw) && $idRaw !== '' ? Uuid::fromString($idRaw) : null,
            regon: Regon::fromString(self::optionalNullableString($data, 'regon')),
            pesel: Pesel::fromString(self::optionalNullableString($data, 'pesel')),
            source: ClientSource::fromString(self::optionalNullableString($data, 'source')),
            rating: ClientRating::fromInt(
                array_key_exists('rating', $data) && $data['rating'] !== null
                    ? (int) $data['rating']
                    : null
            ),
            notes: ClientNotes::fromString(self::optionalNullableString($data, 'notes')),
            status: ClientStatus::fromString($data['status']),
            addressUuid: self::optionalUuid($data['address_uuid'] ?? null),
            lastContactedAt: self::parseOptionalDateTime($data['last_contacted_at'] ?? null),
            nextContactAt: self::parseOptionalDateTime($data['next_contact_at'] ?? null),
            createdAt: self::parseOptionalDateTime($data['created_at'] ?? null),
            updatedAt: self::parseOptionalDateTime($data['updated_at'] ?? null),
        );
    }

    private static function parseOptionalDateTime(mixed $value): ?\DateTimeImmutable
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);

        return $dt !== false ? $dt : null;
    }

    private static function optionalUuid(mixed $value): ?Uuid
    {
        return is_string($value) && $value !== '' ? Uuid::fromString($value) : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function optionalNullableString(array $data, string $key): ?string
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }
        $value = $data[$key];
        if ($value === null) {
            return null;
        }

        return is_string($value) ? $value : null;
    }
}

if (!class_exists('App\\Crm\\Application\\DTO\\ClientDTO', false)) {
    class_alias(ClientDto::class, 'App\Crm\Application\DTO\ClientDTO');
}
