<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Crm;

use App\Crm\Application\Command\UpdateClient\UpdateClientCommand;
use App\Crm\Application\Command\UpdateClient\UpdateClientHandler;
use App\Crm\Domain\Dto\ClientDto;
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

final class UpdateClientHandlerTest extends TestCase
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
        $repository->shouldNotReceive('save');

        $handler = new UpdateClientHandler($repository);
        $command = new UpdateClientCommand(
            id: $uuid,
            name: ClientName::fromString('Updated')
        );

        $this->expectException(ClientNotFoundException::class);

        $handler->handle($command);
    }

    public function test_handle_updates_name_saves_and_returns_dto(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $client = $this->createClient($uuid);

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($client);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (CrmClientAggregate $c) => $c->getName()->getValue() === 'Nowa nazwa'));

        $handler = new UpdateClientHandler($repository);
        $command = new UpdateClientCommand(
            id: $uuid,
            name: ClientName::fromString('Nowa nazwa')
        );

        $result = $handler->handle($command);

        $this->assertInstanceOf(ClientDto::class, $result);
        $this->assertSame('Nowa nazwa', $result->name->getValue());
    }

    public function test_handle_updates_status_saves_and_returns_dto(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $client = $this->createClient($uuid);

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($client);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (CrmClientAggregate $c) => $c->getStatus() === ClientStatus::ARCHIVED));

        $handler = new UpdateClientHandler($repository);
        $command = new UpdateClientCommand(
            id: $uuid,
            status: ClientStatus::ARCHIVED
        );

        $result = $handler->handle($command);

        $this->assertSame(ClientStatus::ARCHIVED, $result->status);
    }

    /** Przypadek brzegowy: idempotencja – ustawienie tej samej wartości, save wywołane z niezmienioną encją */
    public function test_handle_still_saves_when_value_unchanged(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $client = $this->createClient($uuid);
        $originalName = $client->getName()->getValue();
        $clientPassedToSave = null;

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($client);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (CrmClientAggregate $c) use (&$clientPassedToSave, $originalName) {
                $clientPassedToSave = $c;
                return $c->getName()->getValue() === $originalName;
            }));

        $handler = new UpdateClientHandler($repository);
        $command = new UpdateClientCommand(id: $uuid, name: ClientName::fromString($originalName));

        $result = $handler->handle($command);

        $this->assertNotNull($clientPassedToSave, 'save must be called');
        $this->assertSame($originalName, $result->name->getValue(), 'DTO zwraca niezmienioną wartość');
    }

    /** Przypadek brzegowy: clearFields – wywołanie setAddressUuid(null) gdy address_uuid w clearFields */
    public function test_handle_clears_address_uuid_when_in_clear_fields(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $addressUuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440099');
        $client = $this->createClientWithAddress($uuid, $addressUuid);

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($client);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (CrmClientAggregate $c) => $c->getAddressUuid() === null));

        $handler = new UpdateClientHandler($repository);
        $command = new UpdateClientCommand(id: $uuid, clearFields: ['address_uuid']);

        $result = $handler->handle($command);

        $this->assertNull($result->addressUuid);
    }

    /** Przypadek brzegowy: command z samym id (wszystkie pola null) – save nie modyfikuje encji */
    public function test_handle_with_only_id_does_not_modify_entity_but_still_saves(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $client = $this->createClient($uuid);
        $originalName = $client->getName()->getValue();

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($client);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (CrmClientAggregate $c) => $c->getName()->getValue() === $originalName));

        $handler = new UpdateClientHandler($repository);
        $command = new UpdateClientCommand(id: $uuid);

        $result = $handler->handle($command);

        $this->assertSame($originalName, $result->name->getValue());
    }

    private function createClientWithAddress(Uuid $id, Uuid $addressUuid): CrmClientAggregate
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
            addressUuid: $addressUuid,
            addresses: new \Illuminate\Support\Collection(),
            contacts: new \Illuminate\Support\Collection(),
            tags: new \Illuminate\Support\Collection(),
            accounts: new \Illuminate\Support\Collection(),
            clientNoteDto: null,
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
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
            addresses: new \Illuminate\Support\Collection(),
            contacts: new \Illuminate\Support\Collection(),
            tags: new \Illuminate\Support\Collection(),
            accounts: new \Illuminate\Support\Collection(),
            clientNoteDto: null,
            isDeleted: IsDeleted::fromBool(false),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
    }
}
