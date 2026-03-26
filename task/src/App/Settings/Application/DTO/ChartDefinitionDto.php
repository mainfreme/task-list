<?php

declare(strict_types=1);

namespace App\Settings\Application\DTO;

final class ChartDefinitionDto
{
    /**
     * @param array<int|string, mixed> $displayFields
     */
    public function __construct(
        public readonly string $id,
        public readonly string $chartType,
        public readonly array $displayFields,
        public readonly string $sqlQuery,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chart_type' => $this->chartType,
            'display_fields' => $this->displayFields,
            'sql_query' => $this->sqlQuery,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
