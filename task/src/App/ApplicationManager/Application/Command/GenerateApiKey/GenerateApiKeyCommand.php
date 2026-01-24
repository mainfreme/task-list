<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateApiKey;

use App\ApplicationManager\Domain\ValueObject\Uuid;

final class GenerateApiKeyCommand
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
