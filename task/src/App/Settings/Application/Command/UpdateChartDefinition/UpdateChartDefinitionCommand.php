<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpdateChartDefinition;

use App\Settings\Application\Command\SettingsCommandContext;
use App\Shared\Domain\ValueObject\Uuid;

final class UpdateChartDefinitionCommand
{
    /**
     * @param array<int|string, mixed> $displayFields
     */
    public function __construct(
        public readonly Uuid $id,
        public readonly string $chartType,
        public readonly array $displayFields,
        public readonly string $sqlQuery,
        public readonly ?SettingsCommandContext $context = null,
    ) {
    }
}
