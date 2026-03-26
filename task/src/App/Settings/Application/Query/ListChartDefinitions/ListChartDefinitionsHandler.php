<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\ListChartDefinitions;

use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class ListChartDefinitionsHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    /**
     * @return ChartDefinitionDto[]
     */
    public function handle(ListChartDefinitionsQuery $query): array
    {
        return array_map(
            fn ($entity) => $this->mapper->toChartDefinitionDto($entity),
            $this->repository->findAll()
        );
    }
}
