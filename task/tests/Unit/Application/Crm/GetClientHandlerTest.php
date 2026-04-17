<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Crm;

use App\Crm\Application\DTO\ClientDto;
use App\Crm\Application\Query\GetClient\GetClientHandler;
use App\Crm\Application\Query\GetClient\GetClientQuery;
use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Exception\ClientNotFoundException;
use App\Crm\Domain\Repository\ClientRepositoryInterface;
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
use Mockery;
use PHPUnit\Framework\TestCase;

final class GetClientHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_when_client_not_found(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(ClientNotFoundException::byId($uuid->getValue()));

        $handler = new GetClientHandler($repository);
        $query = new GetClientQuery($uuid);

        $this->expectException(ClientNotFoundException::class);
        $this->expectExceptionMessage('Client with ID');

        $handler->handle($query);
    }

    public function test_handle_returns_dto_with_client_data(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $client = $this->createClient($uuid);

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andReturn($client);

        $handler = new GetClientHandler($repository);
        $query = new GetClientQuery($uuid);

        $result = $handler->handle($query);

        $this->assertInstanceOf(ClientDto::class, $result);
        $this->assertSame($uuid->getValue(), $result->id->getValue());
        $this->assertSame('Client do pobrania', $result->name->getValue());
        $this->assertSame(ClientStatus::ACTIVE, $result->status);
    }

    /** Przypadek brzegowy: client z opcjonalnymi polami → DTO zwraca te same wartości */
    public function test_handle_returns_dto_with_optional_fields_when_set_on_entity(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $addressUuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440001');
        $client = $this->createClientWithOptionals($uuid, $addressUuid);

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($client);

        $handler = new GetClientHandler($repository);
        $query = new GetClientQuery($uuid);

        $result = $handler->handle($query);

        $this->assertInstanceOf(ClientDto::class, $result);
        $this->assertSame($addressUuid->getValue(), $result->addressUuid?->getValue());
        $this->assertSame('142345678', $result->regon?->toString());
        $this->assertSame('82031412346', $result->pesel?->toString());
        $this->assertSame('referral', $result->source?->toString());
        $this->assertSame(5, $result->rating?->toInt());
        $this->assertSame('Notatka', $result->notes?->toString());
    }

    private function createClient(Uuid $id): CrmClientAggregate
    {
        return CrmClientAggregate::reconstitute(
            id: $id,
            name: ClientName::fromString('Client do pobrania'),
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
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
    }

    private function createClientWithOptionals(Uuid $id, Uuid $addressUuid): CrmClientAggregate
    {
        return CrmClientAggregate::reconstitute(
            id: $id,
            name: ClientName::fromString('Client z opcjonalnymi'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::PROSPECT,
            isCompany: IsCompany::fromBool(true),
            regon: Regon::fromString('142345678'),
            pesel: Pesel::fromString('82031412346'),
            source: ClientSource::fromString('referral'),
            rating: ClientRating::fromInt(5),
            notes: ClientNotes::fromString('Notatka'),
            lastContactedAt: null,
            nextContactAt: null,
            addressUuid: $addressUuid,
            addresses: [],
            contacts: [],
            tags: [],
            clientNote: null,
            accounts: [],
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
    }
}
