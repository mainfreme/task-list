<?php

declare(strict_types=1);

namespace App\Settings\Application\Cache;

interface SettingsQueryCacheInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function find(string $key): ?array;

    /**
     * @param array<string, mixed> $payload
     */
    public function save(string $key, array $payload): void;

    /**
     * Unieważnia wszystkie warianty cache query modułu Settings.
     */
    public function invalidate(): void;
}
