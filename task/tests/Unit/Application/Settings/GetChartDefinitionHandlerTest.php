<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\Query\GetChartDefinition\GetChartDefinitionHandler;
use App\Settings\Application\Query\GetChartDefinition\GetChartDefinitionQuery;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Exception\ChartDefinitionNotFoundException;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

final class GetChartDefinitionHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_when_not_found(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(ChartDefinitionRepositoryInterface::class);
        $cache = Mockery::mock(SettingsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldNotReceive('save');
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(ChartDefinitionNotFoundException::byId($id->getValue()));

        $handler = new GetChartDefinitionHandler(
            $repository,
            new SettingsEntityMapper(new IntegrationCredentialsMasker()),
            $cache
        );

        $this->expectException(ChartDefinitionNotFoundException::class);

        $handler->handle(new GetChartDefinitionQuery(id: $id));
    }

    public function test_handle_uses_cache_when_payload_contains_empty_sql_query(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $repository = Mockery::mock(ChartDefinitionRepositoryInterface::class);
        $cache = Mockery::mock(SettingsQueryCacheInterface::class);
        $cache->shouldReceive('find')
            ->once()
            ->andReturn([
                'id' => $id->getValue(),
                'chart_type' => 'bar',
                'display_fields' => ['a' => 1],
                'sql_query' => '',
                'created_at' => '2025-02-01T00:00:00+00:00',
                'updated_at' => '2025-02-02T00:00:00+00:00',
            ]);
        $cache->shouldNotReceive('save');
        $repository->shouldNotReceive('findById');

        $handler = new GetChartDefinitionHandler(
            $repository,
            new SettingsEntityMapper(new IntegrationCredentialsMasker()),
            $cache
        );

        $dto = $handler->handle(new GetChartDefinitionQuery(id: $id));

        $this->assertSame($id->getValue(), $dto->id);
        $this->assertSame('bar', $dto->chartType);
        $this->assertSame(['a' => 1], $dto->displayFields);
        $this->assertSame('', $dto->sqlQuery);
    }

    public function test_handle_rebuilds_and_saves_cache_when_miss_occurs(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $entity = ChartDefinition::reconstitute(
            id: $id,
            chartType: 'bar',
            displayFields: ['a' => 1],
            sqlQuery: 'SELECT id FROM t',
            createdAt: new DateTimeImmutable('2025-02-01T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2025-02-02T00:00:00+00:00'),
        );

        $repository = Mockery::mock(ChartDefinitionRepositoryInterface::class);
        $cache = Mockery::mock(SettingsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldReceive('save')
            ->once()
            ->with(
                Mockery::type('string'),
                Mockery::on(fn (array $payload) => ($payload['sql_query'] ?? null) === 'SELECT id FROM t')
            );
        $repository->shouldReceive('findById')->once()->with(Mockery::on(fn ($u) => $u->getValue() === $id->getValue()))->andReturn($entity);

        $handler = new GetChartDefinitionHandler(
            $repository,
            new SettingsEntityMapper(new IntegrationCredentialsMasker()),
            $cache
        );

        $dto = $handler->handle(new GetChartDefinitionQuery(id: $id));

        $this->assertSame('SELECT id FROM t', $dto->sqlQuery);
    }
}
