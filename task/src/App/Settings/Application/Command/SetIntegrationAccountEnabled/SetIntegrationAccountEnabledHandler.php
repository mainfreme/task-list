<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\SetIntegrationAccountEnabled;

use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class SetIntegrationAccountEnabledHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(SetIntegrationAccountEnabledCommand $command): IntegrationAccountDto
    {
        $entity = $this->repository->findById($command->id);
        if ($command->enabled) {
            $entity->enable();
        } else {
            $entity->disable();
        }
        $this->repository->save($entity);

        $loaded = $this->repository->findById($command->id);

        return $this->mapper->toIntegrationAccountDto($loaded);
    }
}
