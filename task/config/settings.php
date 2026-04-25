<?php

declare(strict_types=1);

return [
    'redis_cache' => [
        /*
        | Prefiks kluczy cache query modułu Settings.
        */
        'key_prefix' => (string) env('SETTINGS_REDIS_CACHE_KEY_PREFIX', 'settings:query:'),
        /*
        | Wersja schematu JSON w cache (zmiana struktury payloadu).
        */
        'value_version' => (int) env('SETTINGS_REDIS_CACHE_VALUE_VERSION', 1),
        /*
        | Klucz licznika unieważniającego wszystkie wpisy cache modułu.
        */
        'invalidation_key' => (string) env('SETTINGS_REDIS_CACHE_INVALIDATION_KEY', 'settings:query:invalidation'),
        /*
        | TTL cache w sekundach (domyślnie 3 dni).
        */
        'ttl' => max(1, (int) env('SETTINGS_REDIS_CACHE_TTL', 60 * 60 * 24 * 3)),
    ],
];
