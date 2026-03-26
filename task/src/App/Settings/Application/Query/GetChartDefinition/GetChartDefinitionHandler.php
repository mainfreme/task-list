<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetChartDefinition;

use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class GetChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(GetChartDefinitionQuery $query): ChartDefinitionDto
    {
        $entity = $this->repository->findById($query->id);

        return $this->mapper->toChartDefinitionDto($entity);
    }
}
