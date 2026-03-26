<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\ListIntegrationAccounts;

use App\Settings\Application\DTO\IntegrationAccountSummaryDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class ListIntegrationAccountsHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    /**
     * @return IntegrationAccountSummaryDto[]
     */
    public function handle(ListIntegrationAccountsQuery $query): array
    {
        return array_map(
            fn ($entity) => $this->mapper->toIntegrationAccountSummaryDto($entity),
            $this->repository->findAll()
        );
    }
}
