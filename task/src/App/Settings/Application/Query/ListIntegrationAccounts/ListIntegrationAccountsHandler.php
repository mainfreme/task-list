<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\ListIntegrationAccounts;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\DTO\IntegrationAccountSummaryDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class ListIntegrationAccountsHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsQueryCacheInterface $cache,
    ) {
    }

    /**
     * @return IntegrationAccountSummaryDto[]
     */
    public function handle(ListIntegrationAccountsQuery $query): array
    {
        $cacheKey = 'list-integration-accounts';
        $cached = $this->cache->find($cacheKey);
        if ($cached !== null) {
            $cachedItems = $cached['items'] ?? [];
            if (!is_array($cachedItems)) {
                $cachedItems = [];
            }

            /** @var IntegrationAccountSummaryDto[] $restored */
            $restored = array_map(
                static fn (array $item): IntegrationAccountSummaryDto => IntegrationAccountSummaryDto::fromArray($item),
                $cachedItems
            );

            return $restored;
        }

        $items = array_map(
            fn ($entity) => $this->mapper->toIntegrationAccountSummaryDto($entity),
            $this->repository->findAll()
        );

        $this->cache->save($cacheKey, [
            'items' => array_map(
                static fn (IntegrationAccountSummaryDto $dto): array => $dto->toArray(),
                $items
            ),
        ]);

        return $items;
    }
}
