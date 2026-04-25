<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetChartDefinition;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class GetChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsQueryCacheInterface $cache,
    ) {
    }

    public function handle(GetChartDefinitionQuery $query): ChartDefinitionDto
    {
        $cacheKey = sprintf('get-chart-definition:%s', $query->id->getValue());
        $cached = $this->cache->find($cacheKey);
        if ($cached !== null) {
            return ChartDefinitionDto::fromArray($cached);
        }

        $entity = $this->repository->findById($query->id);
        $dto = $this->mapper->toChartDefinitionDto($entity);
        $this->cache->save($cacheKey, $dto->toArray());

        return $dto;
    }
}
