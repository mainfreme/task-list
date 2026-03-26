<?php

declare(strict_types=1);

namespace App\Settings\Domain\Repository;

use App\Settings\Domain\Entity\ChartDefinition;
use App\Shared\Domain\ValueObject\Uuid;

interface ChartDefinitionRepositoryInterface
{
    public function findById(Uuid $id): ChartDefinition;

    /**
     * @return ChartDefinition[]
     */
    public function findAll(): array;

    public function save(ChartDefinition $definition): void;

    public function delete(ChartDefinition $definition): void;
}
