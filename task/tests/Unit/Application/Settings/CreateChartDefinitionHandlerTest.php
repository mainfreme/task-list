<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\CreateChartDefinition\CreateChartDefinitionCommand;
use App\Settings\Application\Command\CreateChartDefinition\CreateChartDefinitionHandler;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

final class CreateChartDefinitionHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_saves_and_returns_dto_with_same_definition_data(): void
    {
        $mapper = new SettingsEntityMapper(new IntegrationCredentialsMasker());

        $repository = Mockery::mock(ChartDefinitionRepositoryInterface::class);
        $captured = null;

        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (ChartDefinition $e) use (&$captured) {
                $captured = $e;

                return $e->getChartType() === 'line'
                    && $e->getSqlQuery() === 'SELECT 1'
                    && $e->getDisplayFields() === ['x' => 'y'];
            }));

        $repository->shouldReceive('findById')
            ->once()
            ->andReturnUsing(function (Uuid $id) use (&$captured) {
                return ChartDefinition::reconstitute(
                    id: $id,
                    chartType: $captured->getChartType(),
                    displayFields: $captured->getDisplayFields(),
                    sqlQuery: $captured->getSqlQuery(),
                    createdAt: new DateTimeImmutable('2025-01-01T12:00:00+00:00'),
                    updatedAt: new DateTimeImmutable('2025-01-01T12:00:00+00:00'),
                );
            });

        $handler = new CreateChartDefinitionHandler($repository, $mapper);
        $result = $handler->handle(new CreateChartDefinitionCommand(
            chartType: 'line',
            displayFields: ['x' => 'y'],
            sqlQuery: 'SELECT 1',
        ));

        $this->assertSame('line', $result->chartType);
        $this->assertSame(['x' => 'y'], $result->displayFields);
        $this->assertSame('SELECT 1', $result->sqlQuery);
        $this->assertNotSame('', $result->id);
    }
}
