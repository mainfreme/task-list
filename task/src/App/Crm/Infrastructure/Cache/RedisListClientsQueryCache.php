<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Cache;

use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Application\DTO\ClientListDto;
use App\Crm\Application\Query\ListClients\ListClientsQuery;
use App\Shared\Domain\Redis\RedisServiceInterface;
use JsonException;
use Psr\Log\LoggerInterface;
use Throwable;

final class RedisListClientsQueryCache implements ListClientsQueryCacheInterface
{
    public function __construct(
        private readonly RedisServiceInterface $redis,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function find(ListClientsQuery $query): ?ClientListDto
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $raw = $this->redis->get($this->buildKey($query));
            if ($raw === null) {
                return null;
            }

            /** @var array{format_version?: int, payload?: array<string, mixed>} $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

            $formatVersion = $decoded['format_version'] ?? null;
            if ($formatVersion !== $this->valueVersion()) {
                return null;
            }

            $payload = $decoded['payload'] ?? null;
            if (!is_array($payload)) {
                return null;
            }

            return ClientListDto::fromArray($payload);
        } catch (JsonException $e) {
            $this->logger->warning('CRM list cache: invalid JSON, ignoring', [
                'key' => $this->buildKey($query),
                'exception' => $e->getMessage(),
            ]);

            return null;
        } catch (Throwable $e) {
            $this->logger->error('CRM list cache: read failed', [
                'key' => $this->buildKey($query),
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function save(ListClientsQuery $query, ClientListDto $dto): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $wrapped = [
                'format_version' => $this->valueVersion(),
                'payload' => $dto->toArray(),
            ];
            $json = json_encode($wrapped, JSON_THROW_ON_ERROR);
            $this->redis->set($this->buildKey($query), $json, $this->ttlSeconds());
        } catch (Throwable $e) {
            $this->logger->error('CRM list cache: write failed', [
                'key' => $this->buildKey($query),
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function invalidate(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $this->redis->increment($this->invalidationKey());
        } catch (Throwable $e) {
            $this->logger->error('CRM list cache: invalidation failed', [
                'key' => $this->invalidationKey(),
                'exception' => $e->getMessage(),
            ]);
        }
    }

    private function isEnabled(): bool
    {
        return (bool) config('redis_services.cache.enabled');
    }

    private function ttlSeconds(): int
    {
        return (int) config('redis_services.cache.ttl');
    }

    private function valueVersion(): int
    {
        return (int) config('crm.redis_cache.value_version');
    }

    private function keyPrefix(): string
    {
        return (string) config('crm.redis_cache.key_prefix');
    }

    private function invalidationKey(): string
    {
        return (string) config('crm.redis_cache.invalidation_key');
    }

    private function listGeneration(): int
    {
        $raw = $this->redis->get($this->invalidationKey());

        return $raw === null || $raw === '' ? 0 : (int) $raw;
    }

    private function buildKey(ListClientsQuery $query): string
    {
        $statusPart = $query->status !== null ? $query->status->value : 'all';

        return sprintf(
            '%sv%d:g%d:p%d:pp%d:s:%s',
            $this->keyPrefix(),
            $this->valueVersion(),
            $this->listGeneration(),
            $query->page,
            $query->perPage,
            $statusPart
        );
    }
}
