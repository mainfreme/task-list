<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateJwtToken;

use App\ApplicationManager\Domain\ValueObject\Uuid;

final class GenerateJwtTokenCommand
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly ?int $expirationMinutes = null,
    ) {
    }
}
