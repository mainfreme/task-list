<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetSettingEntry;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\DTO\SettingEntryDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class GetSettingEntryHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsQueryCacheInterface $cache,
    ) {
    }

    public function handle(GetSettingEntryQuery $query): SettingEntryDto
    {
        $cacheKey = sprintf('get-setting-entry:%s', $query->id->getValue());
        $cached = $this->cache->find($cacheKey);
        if ($cached !== null) {
            return SettingEntryDto::fromArray($cached);
        }

        $entity = $this->repository->findById($query->id);
        $dto = $this->mapper->toSettingEntryDto($entity);
        $this->cache->save($cacheKey, $dto->toArray());

        return $dto;
    }
}
