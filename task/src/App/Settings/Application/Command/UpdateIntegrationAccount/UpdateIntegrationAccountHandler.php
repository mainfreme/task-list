<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpdateIntegrationAccount;

use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class UpdateIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(UpdateIntegrationAccountCommand $command): IntegrationAccountDto
    {
        $entity = $this->repository->findById($command->id);
        $entity->update(
            $command->name,
            $command->enabled,
            $command->externalAccountId,
            $command->provider,
            $command->credentials,
        );
        $this->repository->save($entity);

        $loaded = $this->repository->findById($command->id);

        return $this->mapper->toIntegrationAccountDto($loaded);
    }
}
