<?php

declare(strict_types=1);

return [
    /*
    | Nazwa połączenia z config/database.php → redis (np. default, cache).
    | Puste = domyślne połączenie Laravel Redis.
    */
    'connection' => env('REDIS_SERVICE_CONNECTION'),

    /*
    | Globalne ustawienia cache Redis dla modułów (TTL, włączenie).
    | Per moduł: prefiks klucza, wersja formatu wartości — w config/{moduł}.php.
    */
    'cache' => [
        'enabled' => filter_var(
            env('REDIS_CACHE_ENABLED', true),
            FILTER_VALIDATE_BOOLEAN
        ),
        'ttl' => max(1, (int) env('REDIS_CACHE_TTL', 3600)),
    ],
];
