<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Cache;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Infrastructure\Mapper\ApplicationManagerEntityMapper;
use App\ApplicationManager\Infrastructure\Repository\EloquentApplicationManagerRepository;
use App\Shared\Domain\Redis\RedisServiceInterface;
use App\Shared\Domain\ValueObject\Uuid;
use JsonException;
use Psr\Log\LoggerInterface;
use Throwable;

final class ApplicationManagerCacheStore
{
    public function __construct(
        private readonly RedisServiceInterface $redis,
        private readonly LoggerInterface $logger,
        private readonly EloquentApplicationManagerRepository $repository
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool) config('redis_services.cache.enabled');
    }

    public function ttlSeconds(): int
    {
        return (int) config('redis_services.cache.ttl');
    }

    public function keyPrefix(): string
    {
        return (string) config('application_manager.redis_cache.key_prefix');
    }

    public function valueVersion(): int
    {
        return (int) config('application_manager.redis_cache.value_version');
    }

    public function buildKey(string $applicationId): string
    {
        return $this->keyPrefix() . $applicationId;
    }

    public function get(string $applicationId): ?ApplicationManager
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $raw = $this->redis->get($this->buildKey($applicationId));
            if ($raw === null) {
                return null;
            }

            $entity = ApplicationManagerEntityMapper::jsonToEntity($raw, $this->valueVersion());

            return $entity;
        } catch (JsonException $e) {
            $this->logger->warning('ApplicationManager cache: invalid JSON, ignoring', [
                'application_id' => $applicationId,
                'exception' => $e->getMessage(),
            ]);

            return null;
        } catch (Throwable $e) {
            $this->logger->error('ApplicationManager cache: read failed', [
                'application_id' => $applicationId,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function put(ApplicationManager $entity): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $id = $entity->getId();
        if ($id === null) {
            return;
        }

        try {
            $json = ApplicationManagerEntityMapper::entityToJson($entity, $this->valueVersion());
            $this->redis->set($this->buildKey($id->getValue()), $json, $this->ttlSeconds());
        } catch (Throwable $e) {
            $this->logger->error('ApplicationManager cache: write failed', [
                'application_id' => $id->getValue(),
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function forget(string $applicationId): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $this->redis->delete($this->buildKey($applicationId));
        } catch (Throwable $e) {
            $this->logger->error('ApplicationManager cache: delete failed', [
                'application_id' => $applicationId,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function warmFromDatabase(string $applicationId): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $entity = $this->repository->findById(Uuid::fromString($applicationId));
            $this->put($entity);
        } catch (ApplicationManagerNotFoundException) {
            $this->forget($applicationId);
        }
    }
}
