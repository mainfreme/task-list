<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\CreateIntegrationAccount;

use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Entity\IntegrationAccount;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class CreateIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
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

        return $this->mapper->toIntegrationAccountDto($loaded);
    }
}
