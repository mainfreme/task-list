<?php

declare(strict_types=1);

return [
    'redis_cache' => [
        /*
        | Prefiks klucza Redis: {prefix}{uuid}
        */
        'key_prefix' => (string) env('APPLICATION_MANAGER_REDIS_CACHE_KEY_PREFIX', 'application-manager:'),
        /*
        | Wersja schematu wartości w cache (zmiana formatu JSON).
        */
        'value_version' => (int) env('APPLICATION_MANAGER_REDIS_CACHE_VALUE_VERSION', 1),
    ],
];
