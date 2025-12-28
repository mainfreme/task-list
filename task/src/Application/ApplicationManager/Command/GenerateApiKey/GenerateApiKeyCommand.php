<?php

declare(strict_types=1);

namespace Application\ApplicationManager\Command\GenerateApiKey;

final class GenerateApiKeyCommand
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}

