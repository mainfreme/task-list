<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteSettingEntry;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class DeleteSettingEntryHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(DeleteSettingEntryCommand $command): void
    {
        $entity = $this->repository->findById($command->id);
        $before = $this->mapper->toSettingEntryDto($entity)->toArray();
        $this->repository->delete($entity);

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'setting_entry',
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
