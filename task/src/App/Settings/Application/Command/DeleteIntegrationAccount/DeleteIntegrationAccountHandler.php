<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteIntegrationAccount;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class DeleteIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(DeleteIntegrationAccountCommand $command): void
    {
        $entity = $this->repository->findById($command->id);
        $before = $this->mapper->toIntegrationAccountDto($entity)->toArray();
        $this->repository->softDelete($command->id);

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'integration_account',
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
