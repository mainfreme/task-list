<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpdateIntegrationAccount;

use App\Settings\Application\Command\SettingsCommandContext;
use App\Shared\Domain\ValueObject\Uuid;

final class UpdateIntegrationAccountCommand
{
    /**
     * @param array<string, mixed> $credentials
     */
    public function __construct(
        public readonly Uuid $id,
        public readonly string $name,
        public readonly bool $enabled,
        public readonly ?string $externalAccountId,
        public readonly ?string $provider,
        public readonly array $credentials,
        public readonly ?SettingsCommandContext $context = null,
    ) {
    }
}
