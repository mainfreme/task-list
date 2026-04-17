<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpdateIntegrationAccount;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class UpdateIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(UpdateIntegrationAccountCommand $command): IntegrationAccountDto
    {
        $entity = $this->repository->findById($command->id);
        $before = $this->mapper->toIntegrationAccountDto($entity)->toArray();
        $entity->update(
            $command->name,
            $command->enabled,
            $command->externalAccountId,
            $command->provider,
            $command->credentials,
        );
        $this->repository->save($entity);

        $loaded = $this->repository->findById($command->id);
        $dto = $this->mapper->toIntegrationAccountDto($loaded);
        $after = $dto->toArray();

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'integration_account',
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
