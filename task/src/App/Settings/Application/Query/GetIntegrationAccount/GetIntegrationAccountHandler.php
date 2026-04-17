<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetIntegrationAccount;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\DTO\IntegrationAccountDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class GetIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
        private readonly SettingsQueryCacheInterface $cache,
    ) {
    }

    public function handle(GetIntegrationAccountQuery $query): IntegrationAccountDto
    {
        $cacheKey = sprintf('get-integration-account:%s', $query->id->getValue());
        $cached = $this->cache->find($cacheKey);
        if ($cached !== null) {
            return IntegrationAccountDto::fromArray($cached);
        }

        $entity = $this->repository->findById($query->id);
        $dto = $this->mapper->toIntegrationAccountDto($entity);
        $this->cache->save($cacheKey, $dto->toArray());

        return $dto;
    }
}
