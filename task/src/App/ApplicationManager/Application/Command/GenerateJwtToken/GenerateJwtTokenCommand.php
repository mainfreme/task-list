<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateJwtToken;

final class GenerateJwtTokenCommand
{
    public function __construct(
        public readonly int $applicationId,
        public readonly ?int $expirationMinutes = null,
    ) {
    }
}
