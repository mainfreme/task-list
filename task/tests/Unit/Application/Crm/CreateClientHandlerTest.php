<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Crm;

use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Application\Command\CreateClient\CreateClientCommand;
use App\Crm\Application\Command\CreateClient\CreateClientHandler;
use App\Crm\Application\DTO\ClientDto;
use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Repository\ClientRepositoryInterface;
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
use Mockery;
use PHPUnit\Framework\TestCase;

final class CreateClientHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_client_with_required_fields_and_saves(): void
    {
        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (CrmClientAggregate $client) {
                return $client->getStatus() === ClientStatus::LEAD;
            }));

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('invalidate')->once();

        $handler = new CreateClientHandler($repository, $cache);
        $command = new CreateClientCommand(
            name: ClientName::fromString('Test Client'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            isCompany: IsCompany::fromBool(true),
        );

        $result = $handler->handle($command);

        $this->assertInstanceOf(ClientDto::class, $result);
        $this->assertSame('Test Client', $result->name->getValue());
        $this->assertSame('5252674798', $result->nip->getValue());
        $this->assertSame('Polska', $result->country->getValue());
        $this->assertSame(ClientStatus::LEAD, $result->status);
        $this->assertTrue($result->isCompany->toBool());
        $this->assertNull($result->regon);
        $this->assertNull($result->source);
        $this->assertNull($result->rating);
    }

    public function test_handle_creates_client_with_all_optional_fields(): void
    {
        $addressUuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440001');

        $repository = Mockery::mock(ClientRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (CrmClientAggregate $client) => true));

        $cache = Mockery::mock(ListClientsQueryCacheInterface::class);
        $cache->shouldReceive('invalidate')->once();

        $handler = new CreateClientHandler($repository, $cache);
        $command = new CreateClientCommand(
            name: ClientName::fromString('Full Client'),
            nip: Nip::fromString('5252674798'),
            country: Country::fromString('Polska'),
            isCompany: IsCompany::fromBool(true),
            regon: Regon::fromString('142345678'),
            pesel: Pesel::fromString('82031412346'),
            source: ClientSource::fromString('referral'),
            rating: ClientRating::fromInt(5),
            notes: ClientNotes::fromString('Notatka'),
            status: ClientStatus::PROSPECT,
            addressUuid: $addressUuid,
        );

        $result = $handler->handle($command);

        $this->assertSame('142345678', $result->regon?->toString());
        $this->assertSame('82031412346', $result->pesel?->toString());
        $this->assertSame('referral', $result->source?->toString());
        $this->assertSame(5, $result->rating?->toInt());
        $this->assertSame('Notatka', $result->notes?->toString());
        $this->assertSame(ClientStatus::PROSPECT, $result->status);
        $this->assertSame($addressUuid->getValue(), $result->addressUuid?->getValue());
    }

}
