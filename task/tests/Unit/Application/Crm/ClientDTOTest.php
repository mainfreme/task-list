<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Crm;

use App\Crm\Application\DTO\ClientDto;
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
use PHPUnit\Framework\TestCase;

final class ClientDTOTest extends TestCase
{
    public function test_to_array_maps_all_fields_correctly(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $addressUuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440001');
        $lastContacted = new \DateTimeImmutable('2025-01-15 10:00:00');
        $nextContact = new \DateTimeImmutable('2025-02-01 14:00:00');
        $createdAt = new \DateTimeImmutable('2025-01-01 08:00:00');
        $updatedAt = new \DateTimeImmutable('2025-01-10 12:00:00');

        $dto = new ClientDto(
            name: ClientName::fromString('Test Client'),
            nip: Nip::tryFrom('5252674798') ?? throw new \LogicException('test NIP'),
            country: Country::fromString('Polska'),
            isCompany: IsCompany::fromBool(true),
            id: $id,
            regon: Regon::fromString('142345678'),
            pesel: Pesel::fromString('82031412346'),
            source: ClientSource::fromString('referral'),
            rating: ClientRating::fromInt(5),
            notes: ClientNotes::fromString('Notatka'),
            status: ClientStatus::ACTIVE,
            addressUuid: $addressUuid,
            lastContactedAt: $lastContacted,
            nextContactAt: $nextContact,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $array = $dto->toArray();

        $this->assertSame($id->getValue(), $array['id']);
        $this->assertSame('Test Client', $array['name']);
        $this->assertSame('5252674798', $array['nip']);
        $this->assertSame('Polska', $array['country']);
        $this->assertSame('active', $array['status']);
        $this->assertTrue($array['is_company']);
        $this->assertSame('142345678', $array['regon']);
        $this->assertSame('82031412346', $array['pesel']);
        $this->assertSame('referral', $array['source']);
        $this->assertSame(5, $array['rating']);
        $this->assertSame('Notatka', $array['notes']);
        $this->assertSame($addressUuid->getValue(), $array['address_uuid']);
        $this->assertSame('2025-01-15 10:00:00', $array['last_contacted_at']);
        $this->assertSame('2025-02-01 14:00:00', $array['next_contact_at']);
        $this->assertSame('2025-01-01 08:00:00', $array['created_at']);
        $this->assertSame('2025-01-10 12:00:00', $array['updated_at']);
    }

    /** Przypadek brzegowy: DTO z wszystkimi opcjonalnymi polami null → toArray zwraca null dla tych pól */
    public function test_to_array_returns_null_for_optional_fields_when_not_set(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $dto = new ClientDto(
            name: ClientName::fromString('Minimal'),
            nip: Nip::tryFrom('5252674798') ?? throw new \LogicException('test NIP'),
            country: Country::fromString('Polska'),
            isCompany: IsCompany::fromBool(false),
            id: $id,
            regon: null,
            pesel: null,
            source: null,
            rating: null,
            notes: null,
            status: ClientStatus::LEAD,
            addressUuid: null,
            lastContactedAt: null,
            nextContactAt: null,
            createdAt: null,
            updatedAt: null,
        );

        $array = $dto->toArray();

        $this->assertNull($array['regon']);
        $this->assertNull($array['pesel']);
        $this->assertNull($array['source']);
        $this->assertNull($array['rating']);
        $this->assertNull($array['notes']);
        $this->assertNull($array['address_uuid']);
        $this->assertNull($array['last_contacted_at']);
        $this->assertNull($array['next_contact_at']);
        $this->assertNull($array['created_at']);
        $this->assertNull($array['updated_at']);
    }

    public function test_to_array_maps_null_nip_for_private_client(): void
    {
        $dto = new ClientDto(
            name: ClientName::fromString('Osoba'),
            nip: null,
            country: Country::fromString('PL'),
            isCompany: IsCompany::fromBool(false),
            id: Uuid::fromString('550e8400-e29b-41d4-a716-446655440000'),
            pesel: Pesel::fromString('82031412346'),
        );

        $array = $dto->toArray();

        $this->assertNull($array['nip']);
        $this->assertSame('82031412346', $array['pesel']);
    }
}
