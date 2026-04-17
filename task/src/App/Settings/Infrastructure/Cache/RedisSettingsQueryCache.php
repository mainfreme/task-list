<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Cache;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Shared\Domain\Redis\RedisServiceInterface;
use JsonException;
use Psr\Log\LoggerInterface;
use Throwable;

final class RedisSettingsQueryCache implements SettingsQueryCacheInterface
{
    public function __construct(
        private readonly RedisServiceInterface $redis,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function find(string $key): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $raw = $this->redis->get($this->buildKey($key));
            if ($raw === null) {
                return null;
            }

            /** @var mixed $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($decoded)) {
                return null;
            }

            $formatVersion = $decoded['format_version'] ?? null;
            if (!is_int($formatVersion) || $formatVersion !== $this->valueVersion()) {
                return null;
            }

            $payload = $decoded['payload'] ?? null;
            if (!is_array($payload)) {
                return null;
            }

            return $payload;
        } catch (JsonException $e) {
            $this->logger->warning('Settings query cache: invalid JSON, ignoring', [
                'key' => $key,
                'exception' => $e->getMessage(),
            ]);

            return null;
        } catch (Throwable $e) {
            $this->logger->error('Settings query cache: read failed', [
                'key' => $key,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function save(string $key, array $payload): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $wrapped = [
                'format_version' => $this->valueVersion(),
                'payload' => $payload,
            ];
            $json = json_encode($wrapped, JSON_THROW_ON_ERROR);
            $this->redis->set($this->buildKey($key), $json, $this->ttlSeconds());
        } catch (Throwable $e) {
            $this->logger->error('Settings query cache: write failed', [
                'key' => $key,
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
            $this->logger->error('Settings query cache: invalidation failed', [
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
        return max(1, (int) config('settings.redis_cache.ttl', 60 * 60 * 24 * 3));
    }

    private function valueVersion(): int
    {
        return (int) config('settings.redis_cache.value_version');
    }

    private function keyPrefix(): string
    {
        return (string) config('settings.redis_cache.key_prefix');
    }

    private function invalidationKey(): string
    {
        return (string) config('settings.redis_cache.invalidation_key');
    }

    private function generation(): int
    {
        $raw = $this->redis->get($this->invalidationKey());

        return $raw === null || $raw === '' ? 0 : (int) $raw;
    }

    private function buildKey(string $key): string
    {
        return sprintf(
            '%sv%d:g%d:%s',
            $this->keyPrefix(),
            $this->valueVersion(),
            $this->generation(),
            $key
        );
    }
}
