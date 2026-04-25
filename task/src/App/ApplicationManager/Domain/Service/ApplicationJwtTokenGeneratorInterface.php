<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\Service;

/**
 * Port wyjściowy: generowanie tokenów JWT dla aplikacji (implementacja w Infrastructure).
 */
interface ApplicationJwtTokenGeneratorInterface
{
    public function defaultExpirationMinutes(): int;

    public function generate(string $applicationId, string $applicationName, int $expirationMinutes): string;
}
