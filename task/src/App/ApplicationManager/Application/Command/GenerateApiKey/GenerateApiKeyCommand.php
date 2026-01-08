<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateApiKey;

final class GenerateApiKeyCommand
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}
