<?php

declare(strict_types=1);

namespace App\Settings\Application\Command;

final readonly class SettingsCommandContext
{
    public function __construct(
        public ?string $actorId = null,
        public ?string $requestUrl = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {
    }
}
