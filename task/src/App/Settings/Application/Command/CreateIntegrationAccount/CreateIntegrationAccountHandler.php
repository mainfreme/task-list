<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\CreateIntegrationAccount;

use App\Settings\Application\Command\SettingsChangeDetector;
use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Entity\IntegrationAccount;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class CreateIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsEventDispatcherInterface $events,
    ) {
    }

    public function handle(CreateIntegrationAccountCommand $command): IntegrationAccountDto
    {
        $entity = IntegrationAccount::create(
            $command->name,
            $command->enabled,
            $command->externalAccountId,
            $command->provider,
            $command->credentials,
        );
        $this->repository->save($entity);

        $loaded = $this->repository->findById($entity->getId());
        $dto = $this->mapper->toIntegrationAccountDto($loaded);
        $after = $dto->toArray();

        $this->events->dispatch(new SettingsChangedEvent(
            resourceType: 'integration_account',
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
