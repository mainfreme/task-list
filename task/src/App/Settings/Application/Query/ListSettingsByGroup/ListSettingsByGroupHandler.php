<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\ListSettingsByGroup;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\DTO\SettingEntryDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class ListSettingsByGroupHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsQueryCacheInterface $cache,
    ) {
    }

    /**
     * @return SettingEntryDto[]
     */
    public function handle(ListSettingsByGroupQuery $query): array
    {
        $cacheKey = sprintf('list-settings-by-group:%s', $query->groupKey);
        $cached = $this->cache->find($cacheKey);
        if ($cached !== null) {
            $cachedItems = $cached['items'] ?? [];
            if (!is_array($cachedItems)) {
                $cachedItems = [];
            }

            /** @var SettingEntryDto[] $restored */
            $restored = array_map(
                static fn (array $item): SettingEntryDto => SettingEntryDto::fromArray($item),
                $cachedItems
            );

            return $restored;
        }

        $items = array_map(
            fn ($entity) => $this->mapper->toSettingEntryDto($entity),
            $this->repository->findByGroup($query->groupKey)
        );

        $this->cache->save($cacheKey, [
            'items' => array_map(
                static fn (SettingEntryDto $dto): array => $dto->toArray(),
                $items
            ),
        ]);

        return $items;
    }
}
