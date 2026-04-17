<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\CreateIntegrationAccount;

use App\Settings\Application\Command\SettingsCommandContext;

final class CreateIntegrationAccountCommand
{
    /**
     * @param array<string, mixed> $credentials
     */
    public function __construct(
        public readonly string $name,
        public readonly bool $enabled,
        public readonly ?string $externalAccountId,
        public readonly ?string $provider,
        public readonly array $credentials,
        public readonly ?SettingsCommandContext $context = null,
    ) {
    }
}
