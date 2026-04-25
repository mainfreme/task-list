<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Crm;

use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Application\DTO\ClientDto;
use App\Crm\Application\DTO\ClientListDto;
use App\Crm\Application\Query\ListClients\ListClientsHandler;
use App\Crm\Application\Query\ListClients\ListClientsQuery;
use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Repository\ClientRepositoryInterface;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\Nip;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ListClientsHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_returns_list_dto_with_clients_from_find_all(): void
    {
        $clients = [
            $this->createClient(Uuid::fromString('550e8400-e29b-41d4-a716-446655440000')),
            $this->createClient(Uuid::fromString('550e8400-e29b-41d4-a716-446655440001')),
        ];

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findAll')
            ->once()
            ->with(20, 0)
            ->andReturn($clients);
        $repository->shouldReceive('count')->once()->andReturn(2);

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldReceive('save')->once();

        $handler = new ListClientsHandler($repository, $cache);
        $query = new ListClientsQuery(page: 1, perPage: 20, status: null);

        $result = $handler->handle($query);

        $this->assertInstanceOf(ClientListDto::class, $result);
        $this->assertCount(2, $result->clients);
        $this->assertSame(2, $result->total);
        $this->assertSame(1, $result->page);
        $this->assertSame(20, $result->perPage);
        $this->assertSame(1, $result->totalPages);
    }

    public function test_handle_uses_find_by_status_when_status_filter_provided(): void
    {
        $clients = [
            $this->createClient(Uuid::fromString('550e8400-e29b-41d4-a716-446655440000')),
        ];

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findByStatus')
            ->once()
            ->with(ClientStatus::ACTIVE, 20, 0)
            ->andReturn($clients);
        $repository->shouldReceive('countByStatus')->once()->with(ClientStatus::ACTIVE)->andReturn(1);

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldReceive('save')->once();

        $handler = new ListClientsHandler($repository, $cache);
        $query = new ListClientsQuery(page: 1, perPage: 20, status: ClientStatus::ACTIVE);

        $result = $handler->handle($query);

        $this->assertInstanceOf(ClientListDto::class, $result);
        $this->assertCount(1, $result->clients);
        $this->assertInstanceOf(ClientDto::class, $result->clients[0]);
        $this->assertSame(ClientStatus::ACTIVE, $result->clients[0]->status);
    }

    public function test_handle_applies_pagination_offset(): void
    {
        $clients = [];

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findAll')
            ->once()
            ->with(10, 10)
            ->andReturn($clients);
        $repository->shouldReceive('count')->once()->andReturn(25);

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldReceive('save')->once();

        $handler = new ListClientsHandler($repository, $cache);
        $query = new ListClientsQuery(page: 2, perPage: 10, status: null);

        $result = $handler->handle($query);

        $this->assertSame(2, $result->page);
        $this->assertSame(10, $result->perPage);
        $this->assertSame(25, $result->total);
        $this->assertSame(3, $result->totalPages);
    }

    /** Mapowanie encja → DTO → toArray: dane klienta muszą być w data */
    public function test_handle_to_array_maps_entity_data_to_response_structure(): void
    {
        $clientId = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $clients = [$this->createClient($clientId)];

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findAll')->once()->andReturn($clients);
        $repository->shouldReceive('count')->once()->andReturn(1);

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldReceive('save')->once();

        $handler = new ListClientsHandler($repository, $cache);
        $query = new ListClientsQuery(page: 1, perPage: 20, status: null);

        $result = $handler->handle($query);
        $array = $result->toArray();

        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('meta', $array);
        $this->assertCount(1, $array['data']);
        $this->assertSame($clientId->getValue(), $array['data'][0]['id']);
        $this->assertSame('Test Client', $array['data'][0]['name']);
        $this->assertSame('active', $array['data'][0]['status']);
        $this->assertSame(1, $array['meta']['total']);
        $this->assertSame(1, $array['meta']['total_pages']);
    }

    /** Przypadek graniczny: pusty wynik (0 klientów) – data pusta, totalPages może być 0 */
    public function test_handle_returns_empty_data_when_no_clients(): void
    {
        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findAll')->once()->with(20, 0)->andReturn([]);
        $repository->shouldReceive('count')->once()->andReturn(0);

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldReceive('save')->once();

        $handler = new ListClientsHandler($repository, $cache);
        $query = new ListClientsQuery(page: 1, perPage: 20, status: null);

        $result = $handler->handle($query);
        $array = $result->toArray();

        $this->assertEmpty($array['data']);
        $this->assertSame(0, $result->total);
        $this->assertSame(0, $result->totalPages);
    }

    /** Cache trafiony: repozytorium nie jest odpytywane, zapis cache nie jest wywoływany */
    public function test_handle_returns_cached_dto_without_querying_repository(): void
    {
        $clientId = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $aggregate = $this->createClient($clientId);
        $cached = new ClientListDto(
            clients: [ClientDto::fromAggregate($aggregate)],
            total: 1,
            page: 1,
            perPage: 20,
            totalPages: 1,
        );

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldNotReceive('findAll');
        $repository->shouldNotReceive('findByStatus');
        $repository->shouldNotReceive('count');
        $repository->shouldNotReceive('countByStatus');

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn($cached);
        $cache->shouldNotReceive('save');

        $handler = new ListClientsHandler($repository, $cache);
        $query = new ListClientsQuery(page: 1, perPage: 20, status: null);

        $result = $handler->handle($query);

        $this->assertSame($cached, $result);
    }

    private function createClient(Uuid $id): CrmClientAggregate
    {
        return CrmClientAggregate::reconstitute(
            id: $id,
            name: ClientName::fromString('Test Client'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            status: ClientStatus::ACTIVE,
            isCompany: IsCompany::fromBool(false),
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
}
