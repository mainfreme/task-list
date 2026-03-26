<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetIntegrationAccount;

use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class GetIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(GetIntegrationAccountQuery $query): IntegrationAccountDto
    {
        $entity = $this->repository->findById($query->id);

        return $this->mapper->toIntegrationAccountDto($entity);
    }
}
