<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\UpdateChartDefinition;

use App\Settings\Application\DTO\ChartDefinitionDto;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class UpdateChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
        private readonly SettingsEntityMapper $mapper,
    ) {
    }

    public function handle(UpdateChartDefinitionCommand $command): ChartDefinitionDto
    {
        $entity = $this->repository->findById($command->id);
        $entity->update($command->chartType, $command->displayFields, $command->sqlQuery);
        $this->repository->save($entity);

        $loaded = $this->repository->findById($command->id);

        return $this->mapper->toChartDefinitionDto($loaded);
    }
}
