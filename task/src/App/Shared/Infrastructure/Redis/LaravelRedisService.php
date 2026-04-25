<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Redis;

use App\Shared\Domain\Redis\RedisServiceInterface;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

final class LaravelRedisService implements RedisServiceInterface
{
    public function __construct(
        private readonly ?string $connectionName = null
    ) {
    }

    public function get(string $key): ?string
    {
        $value = $this->connection()->get($key);

        return $value === false || $value === null ? null : (string) $value;
    }

    public function set(string $key, string $value, ?int $ttlSeconds = null): void
    {
        if ($ttlSeconds !== null && $ttlSeconds > 0) {
            $this->connection()->set($key, $value, 'EX', $ttlSeconds);

            return;
        }

        $this->connection()->set($key, $value);
    }

    public function delete(string $key): void
    {
        $this->connection()->del($key);
    }

    public function increment(string $key): int
    {
        return (int) $this->connection()->incr($key);
    }

    private function connection(): Connection
    {
        if ($this->connectionName !== null && $this->connectionName !== '') {
            return Redis::connection($this->connectionName);
        }

        return Redis::connection();
    }
}
