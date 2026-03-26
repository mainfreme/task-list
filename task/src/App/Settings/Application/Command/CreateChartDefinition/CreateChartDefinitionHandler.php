<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\CreateChartDefinition;

use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Entity\ChartDefinition;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class CreateChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(CreateChartDefinitionCommand $command): ChartDefinitionDto
    {
        $entity = ChartDefinition::create(
            $command->chartType,
            $command->displayFields,
            $command->sqlQuery,
        );
        $this->repository->save($entity);

        $loaded = $this->repository->findById($entity->getId());

        return $this->mapper->toChartDefinitionDto($loaded);
    }
}
