<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\ListChartDefinitions;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class ListChartDefinitionsHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsQueryCacheInterface $cache,
    ) {
    }

    /**
     * @return ChartDefinitionDto[]
     */
    public function handle(ListChartDefinitionsQuery $query): array
    {
        $cacheKey = 'list-chart-definitions';
        $cached = $this->cache->find($cacheKey);
        if ($cached !== null) {
            $cachedItems = $cached['items'] ?? [];
            if (!is_array($cachedItems)) {
                $cachedItems = [];
            }

            /** @var ChartDefinitionDto[] $restored */
            $restored = array_map(
                static fn (array $item): ChartDefinitionDto => ChartDefinitionDto::fromArray($item),
                $cachedItems
            );

            return $restored;
        }

        $items = array_map(
            fn ($entity) => $this->mapper->toChartDefinitionDto($entity),
            $this->repository->findAll()
        );

        $this->cache->save($cacheKey, [
            'items' => array_map(
                static fn (ChartDefinitionDto $dto): array => $dto->toArray(),
                $items
            ),
        ]);

        return $items;
    }
}
