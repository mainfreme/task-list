<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\CreateApplicationManager;

final class CreateApplicationManagerCommand
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $requestUrl = null,
        public readonly bool $isActive = true,
        public readonly ?array $ipWhitelist = null,
    ) {
    }
}
