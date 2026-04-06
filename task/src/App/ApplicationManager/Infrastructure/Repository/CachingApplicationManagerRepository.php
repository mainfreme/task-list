<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Repository;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Infrastructure\Cache\ApplicationManagerCacheStore;
use App\Shared\Domain\ValueObject\Uuid;

final class CachingApplicationManagerRepository implements ApplicationManagerRepositoryInterface
{
    public function __construct(
        private readonly EloquentApplicationManagerRepository $inner,
        private readonly ApplicationManagerCacheStore $cacheStore
    ) {
    }

    public function findById(Uuid $id): ApplicationManager
    {
        if ($this->cacheStore->isEnabled()) {
            $cached = $this->cacheStore->get($id->getValue());
            if ($cached !== null) {
                return $cached;
            }
        }

        $entity = $this->inner->findById($id);
        $this->cacheStore->put($entity);

        return $entity;
    }

    public function findByApiKey(ApiKey $apiKey): ApplicationManager
    {
        $entity = $this->inner->findByApiKey($apiKey);
        $this->cacheStore->put($entity);

        return $entity;
    }

    public function save(ApplicationManager $applicationManager): void
    {
        $this->inner->save($applicationManager);
    }

    public function delete(ApplicationManager $applicationManager): void
    {
        $this->inner->delete($applicationManager);
    }

    public function findAll(): array
    {
        $entities = $this->inner->findAll();

        if ($this->cacheStore->isEnabled()) {
            foreach ($entities as $entity) {
                $this->cacheStore->put($entity);
            }
        }

        return $entities;
    }
}
