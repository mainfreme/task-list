<?php

declare(strict_types=1);

return [
    'redis_cache' => [
        /*
        | Prefiks kluczy listy klientów (CRM): {prefix}v{format}:g{generation}:...
        */
        'key_prefix' => (string) env('CRM_REDIS_CACHE_KEY_PREFIX', 'crm:clients:list:'),
        /*
        | Wersja schematu JSON w cache (zmiana struktury odpowiedzi listy).
        */
        'value_version' => (int) env('CRM_REDIS_CACHE_VALUE_VERSION', 1),
        /*
        | Klucz licznika unieważniającego wszystkie warianty listy (INCR przy mutacji).
        */
        'invalidation_key' => (string) env('CRM_REDIS_CACHE_INVALIDATION_KEY', 'crm:clients:list:invalidation'),
    ],
];
