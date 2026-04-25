<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\SetIntegrationAccountEnabled;

use App\Settings\Application\Command\SettingsCommandContext;
use App\Shared\Domain\ValueObject\Uuid;

final class SetIntegrationAccountEnabledCommand
{
    public function __construct(
        public readonly Uuid $id,
        public readonly bool $enabled,
        public readonly ?SettingsCommandContext $context = null,
    ) {
    }
}
