<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Command\CreateChartDefinition\CreateChartDefinitionCommand;
use App\Settings\Application\Command\CreateChartDefinition\CreateChartDefinitionHandler;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Event\SettingsChangedEvent;
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

    public function test_handle_dispatches_created_event_with_nested_display_fields(): void
    {
        $mapper = new SettingsEntityMapper(new IntegrationCredentialsMasker());
        $events = Mockery::mock(SettingsEventDispatcherInterface::class);

        $repository = Mockery::mock(ChartDefinitionRepositoryInterface::class);
        $captured = null;

        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (ChartDefinition $e) use (&$captured) {
                $captured = $e;

                return $e->getChartType() === 'line'
                    && $e->getSqlQuery() === 'SELECT 1'
                    && $e->getDisplayFields() === ['x' => 'y', 'meta' => ['tooltip' => true]];
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

        $events->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::on(function (SettingsChangedEvent $event) {
                return $event->operation === SettingsChangedEvent::OPERATION_CREATED
                    && $event->before === null
                    && ($event->after['display_fields']['meta']['tooltip'] ?? null) === true
                    && in_array('display_fields', $event->changedFields, true);
            }));

        $handler = new CreateChartDefinitionHandler($repository, $mapper, $events);
        $result = $handler->handle(new CreateChartDefinitionCommand(
            chartType: 'line',
            displayFields: ['x' => 'y', 'meta' => ['tooltip' => true]],
            sqlQuery: 'SELECT 1',
        ));

        $this->assertTrue(in_array('meta', array_keys($result->displayFields), true));
    }
}
