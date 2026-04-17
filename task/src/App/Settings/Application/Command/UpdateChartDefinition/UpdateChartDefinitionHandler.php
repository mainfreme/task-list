<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpdateChartDefinition;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class UpdateChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(UpdateChartDefinitionCommand $command): ChartDefinitionDto
    {
        $entity = $this->repository->findById($command->id);
        $before = $this->mapper->toChartDefinitionDto($entity)->toArray();
        $entity->update($command->chartType, $command->displayFields, $command->sqlQuery);
        $this->repository->save($entity);

        $loaded = $this->repository->findById($command->id);
        $dto = $this->mapper->toChartDefinitionDto($loaded);
        $after = $dto->toArray();

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'chart_definition',
            resourceId: $command->id->getValue(),
            operation: SettingsChangedEvent::OPERATION_UPDATED,
            before: $before,
            after: $after,
            changedFields: SettingsChangeDetector::changedFields($before, $after),
            actorId: $command->context?->actorId,
            requestUrl: $command->context?->requestUrl,
            ipAddress: $command->context?->ipAddress,
            userAgent: $command->context?->userAgent,
        ));

        return $dto;
    }
}
