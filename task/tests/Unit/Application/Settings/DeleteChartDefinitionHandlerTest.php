<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\DeleteChartDefinition\DeleteChartDefinitionCommand;
use App\Settings\Application\Command\DeleteChartDefinition\DeleteChartDefinitionHandler;
use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Exception\ChartDefinitionNotFoundException;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

final class DeleteChartDefinitionHandlerTest extends TestCase
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
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(ChartDefinitionNotFoundException::byId($id->getValue()));
        $repository->shouldNotReceive('delete');

        $handler = new DeleteChartDefinitionHandler($repository);

        $this->expectException(ChartDefinitionNotFoundException::class);

        $handler->handle(new DeleteChartDefinitionCommand(id: $id));
    }

    public function test_handle_deletes_when_found(): void
    {
        $this->expectNotToPerformAssertions();

        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $entity = ChartDefinition::reconstitute(
            id: $id,
            chartType: 'line',
            displayFields: [],
            sqlQuery: 'SELECT 1',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        $repository = Mockery::mock(ChartDefinitionRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entity);
        $repository->shouldReceive('delete')->once()->with(Mockery::on(fn (ChartDefinition $e) => $e->getId()->getValue() === $id->getValue()));

        $handler = new DeleteChartDefinitionHandler($repository);
        $handler->handle(new DeleteChartDefinitionCommand(id: $id));
    }
}
