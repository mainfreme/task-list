<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm;

use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Dto\ClientDto;
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
use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class CrmClientAggregateTest extends TestCase
{
    /** create() ustawia domyślny stan: LEAD, isDelete=false – weryfikacja zachowania factory */
    public function test_create_sets_default_state_lead_and_not_deleted(): void
    {
        $dto = new ClientDto(
            name: ClientName::fromString('Test Client'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            isCompany: IsCompany::fromBool(true),
        );

        $client = CrmClientAggregate::create($dto);

        $this->assertSame(ClientStatus::LEAD, $client->getStatus());
        $this->assertFalse($client->isDelete()->toBool());
        $this->assertNotNull($client->getId());
    }

    /** setStatus zmienia status – weryfikacja wszystkich przejść z LEAD */
    public function test_set_status_changes_status_through_all_transitions(): void
    {
        $client = $this->createClient();
        $this->assertSame(ClientStatus::LEAD, $client->getStatus());

        foreach ([ClientStatus::PROSPECT, ClientStatus::ACTIVE, ClientStatus::INACTIVE, ClientStatus::ARCHIVED] as $status) {
            $client->setStatus($status);
            $this->assertSame($status, $client->getStatus());
        }
    }

    public function test_reconstitute_preserves_given_status(): void
    {
        $client = CrmClientAggregate::reconstitute(
            id: Uuid::generate(),
            name: ClientName::fromString('Test'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::ACTIVE,
            isCompany: IsCompany::fromBool(true),
            regon: null,
            pesel: null,
            source: null,
            rating: null,
            notes: null,
            lastContactedAt: null,
            nextContactAt: null,
            addressUuid: null,
            addresses: new Collection(),
            contacts: new Collection(),
            tags: new Collection(),
            accounts: new Collection(),
            clientNoteDto: null,
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );

        $this->assertSame(ClientStatus::ACTIVE, $client->getStatus());
    }

    public function test_soft_delete_sets_is_deleted(): void
    {
        $client = $this->createClient();

        $client->softDelete();

        $this->assertTrue($client->isDelete()->toBool());
    }

    public function test_reconstitute_with_all_optional_fields_preserves_values(): void
    {
        $addressUuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $lastContacted = new \DateTimeImmutable('2025-01-15 10:00:00');
        $nextContact = new \DateTimeImmutable('2025-02-01 14:00:00');

        $client = CrmClientAggregate::reconstitute(
            id: Uuid::generate(),
            name: ClientName::fromString('Full Client'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::PROSPECT,
            isCompany: IsCompany::fromBool(true),
            regon: Regon::fromString('142345678'),
            pesel: Pesel::fromString('82031412346'),
            source: ClientSource::fromString('referral'),
            rating: ClientRating::fromInt(5),
            notes: ClientNotes::fromString('Notatka'),
            lastContactedAt: $lastContacted,
            nextContactAt: $nextContact,
            addressUuid: $addressUuid,
            addresses: new Collection(),
            contacts: new Collection(),
            tags: new Collection(),
            accounts: new Collection(),
            clientNoteDto: null,
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );

        $this->assertSame($addressUuid->getValue(), $client->getAddressUuid()?->getValue());
        $this->assertSame('142345678', $client->getRegon()?->toString());
        $this->assertSame('82031412346', $client->getPesel()?->toString());
        $this->assertSame('referral', $client->getSource()?->toString());
        $this->assertSame(5, $client->getRating()?->toInt());
        $this->assertSame('Notatka', $client->getNotes()?->toString());
        $this->assertSame('2025-01-15 10:00:00', $client->getLastContactedAt()?->format('Y-m-d H:i:s'));
        $this->assertSame('2025-02-01 14:00:00', $client->getNextContactAt()?->format('Y-m-d H:i:s'));
    }

    private function createClient(): CrmClientAggregate
    {
        $dto = new ClientDto(
            name: ClientName::fromString('Test'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            isCompany: IsCompany::fromBool(false),
        );

        return CrmClientAggregate::create($dto);
    }
}
