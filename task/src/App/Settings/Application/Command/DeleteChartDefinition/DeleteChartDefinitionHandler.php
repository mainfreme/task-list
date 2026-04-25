<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteChartDefinition;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class DeleteChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(DeleteChartDefinitionCommand $command): void
    {
        $entity = $this->repository->findById($command->id);
        $before = $this->mapper->toChartDefinitionDto($entity)->toArray();
        $this->repository->delete($entity);

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'chart_definition',
            resourceId: $command->id->getValue(),
            operation: SettingsChangedEvent::OPERATION_DELETED,
            before: $before,
            after: null,
            changedFields: SettingsChangeDetector::changedFields($before, null),
            actorId: $command->context?->actorId,
            requestUrl: $command->context?->requestUrl,
            ipAddress: $command->context?->ipAddress,
            userAgent: $command->context?->userAgent,
        ));
    }
}
