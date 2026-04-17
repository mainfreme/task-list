<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\CreateChartDefinition;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class CreateChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(CreateChartDefinitionCommand $command): ChartDefinitionDto
    {
        $entity = ChartDefinition::create(
            $command->chartType,
            $command->displayFields,
            $command->sqlQuery,
        );
        $this->repository->save($entity);

        $loaded = $this->repository->findById($entity->getId());
        $dto = $this->mapper->toChartDefinitionDto($loaded);
        $after = $dto->toArray();

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'chart_definition',
            resourceId: $entity->getId()->getValue(),
            operation: SettingsChangedEvent::OPERATION_CREATED,
            before: null,
            after: $after,
            changedFields: SettingsChangeDetector::changedFields(null, $after),
            actorId: $command->context?->actorId,
            requestUrl: $command->context?->requestUrl,
            ipAddress: $command->context?->ipAddress,
            userAgent: $command->context?->userAgent,
        ));

        return $dto;
    }
}
