<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\CreateChartDefinition;

final class CreateChartDefinitionCommand
{
    /**
     * @param array<int|string, mixed> $displayFields
     */
    public function __construct(
        public readonly string $chartType,
        public readonly array $displayFields,
        public readonly string $sqlQuery,
    ) {
    }
}
