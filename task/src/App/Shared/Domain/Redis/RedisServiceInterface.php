<?php

declare(strict_types=1);

namespace App\Shared\Domain\Redis;

interface RedisServiceInterface
{
    public function get(string $key): ?string;

    public function set(string $key, string $value, ?int $ttlSeconds = null): void;

    public function delete(string $key): void;

    /**
     * Atomowo zwiększa wartość klucza (brak klucza traktowany jak 0). Zwraca nową wartość.
     */
    public function increment(string $key): int;
}
