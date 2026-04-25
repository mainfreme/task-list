<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Crm;

use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Application\Command\DeleteClient\DeleteClientCommand;
use App\Crm\Application\Command\DeleteClient\DeleteClientHandler;
use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Exception\ClientNotFoundException;
use App\Crm\Domain\Repository\ClientRepositoryInterface;
use App\Crm\Domain\ValueObject\ClientName;
use App\Crm\Domain\ValueObject\Country;
use App\Crm\Domain\ValueObject\IsCompany;
use App\Crm\Domain\ValueObject\IsDeleted;
use App\Crm\Domain\ValueObject\Nip;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class DeleteClientHandlerTest extends TestCase
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
        $repository->shouldNotReceive('softDelete');

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldNotReceive('invalidate');

        $handler = new DeleteClientHandler($repository, $cache);
        $command = new DeleteClientCommand($uuid);

        $this->expectException(ClientNotFoundException::class);
        $this->expectExceptionMessage('Client with ID');

        $handler->handle($command);
    }

    /** Handler wywołuje softDelete dokładnie raz z clientem załadowanym po id (ten sam id co w komendzie) */
    public function test_handle_calls_soft_delete_with_client_loaded_by_given_id(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $client = $this->createClient($uuid);
        $clientPassedToSoftDelete = null;

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->getValue() === $uuid->getValue()))
            ->andReturn($client);
        $repository->shouldReceive('softDelete')
            ->once()
            ->with(Mockery::on(function (CrmClientAggregate $c) use ($uuid, &$clientPassedToSoftDelete) {
                $clientPassedToSoftDelete = $c;

                return $c->getId() !== null && $c->getId()->getValue() === $uuid->getValue();
            }));

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('invalidate')->once();

        $handler = new DeleteClientHandler($repository, $cache);
        $command = new DeleteClientCommand($uuid);

        $handler->handle($command);

        $this->assertSame($uuid->getValue(), $clientPassedToSoftDelete->getId()?->getValue(), 'softDelete must be called with client having the given id');
    }

    private function createClient(Uuid $id): CrmClientAggregate
    {
        return CrmClientAggregate::reconstitute(
            id: $id,
            name: ClientName::fromString('Test'),
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
