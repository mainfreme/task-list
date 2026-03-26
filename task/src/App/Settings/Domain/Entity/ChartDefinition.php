<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class ChartDefinition
{
    /**
     * @param array<int|string, mixed> $displayFields
     */
    private function __construct(
        private Uuid $id,
        private string $chartType,
        private array $displayFields,
        private string $sqlQuery,
        private ?DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @param array<int|string, mixed> $displayFields
     */
    public static function create(
        string $chartType,
        array $displayFields,
        string $sqlQuery,
    ): self {
        return new self(
            Uuid::generate(),
            $chartType,
            $displayFields,
            $sqlQuery,
            null,
            null,
        );
    }

    /**
     * @param array<int|string, mixed> $displayFields
     */
    public static function reconstitute(
        Uuid $id,
        string $chartType,
        array $displayFields,
        string $sqlQuery,
        ?DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $chartType, $displayFields, $sqlQuery, $createdAt, $updatedAt);
    }

    /**
     * @param array<int|string, mixed> $displayFields
     */
    public function update(string $chartType, array $displayFields, string $sqlQuery): void
    {
        $this->chartType = $chartType;
        $this->displayFields = $displayFields;
        $this->sqlQuery = $sqlQuery;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getChartType(): string
    {
        return $this->chartType;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getDisplayFields(): array
    {
        return $this->displayFields;
    }

    public function getSqlQuery(): string
    {
        return $this->sqlQuery;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
