<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteChartDefinition;

use App\Settings\Application\Command\SettingsCommandContext;
use App\Shared\Domain\ValueObject\Uuid;

final class DeleteChartDefinitionCommand
{
    public function __construct(
        public readonly Uuid $id,
        public readonly ?SettingsCommandContext $context = null,
    ) {
    }
}
