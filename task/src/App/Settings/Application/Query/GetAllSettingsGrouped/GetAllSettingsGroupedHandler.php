<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetAllSettingsGrouped;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class GetAllSettingsGroupedHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsQueryCacheInterface $cache,
    ) {
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function handle(GetAllSettingsGroupedQuery $query): array
    {
        $cacheKey = 'get-all-settings-grouped';
        $cached = $this->cache->find($cacheKey);
        if ($cached !== null) {
            $cachedGroups = $cached['groups'] ?? [];
            if (!is_array($cachedGroups)) {
                $cachedGroups = [];
            }

            /** @var array<string, array<int, array<string, mixed>>> $groups */
            $groups = $cachedGroups;

            return $groups;
        }

        $grouped = $this->repository->findAllGroupedByGroupKey();
        $out = [];
        foreach ($grouped as $groupKey => $entries) {
            $out[$groupKey] = array_map(
                fn ($entity) => $this->mapper->toSettingEntryDto($entity)->toArray(),
                $entries
            );
        }

        $this->cache->save($cacheKey, ['groups' => $out]);

        return $out;
    }
}
