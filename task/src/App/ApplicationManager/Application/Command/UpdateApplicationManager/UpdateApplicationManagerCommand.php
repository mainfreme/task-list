<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\UpdateApplicationManager;

final class UpdateApplicationManagerCommand
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?string $requestUrl = null,
        public readonly ?bool $isActive = null,
        public readonly ?array $ipWhitelist = null,
    ) {
    }
}
