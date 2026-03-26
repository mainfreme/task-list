<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteChartDefinition;

use App\Settings\Domain\Repository\ChartDefinitionRepositoryInterface;

final class DeleteChartDefinitionHandler
{
    public function __construct(
        private readonly ChartDefinitionRepositoryInterface $repository,
    ) {
    }

    public function handle(DeleteChartDefinitionCommand $command): void
    {
        $entity = $this->repository->findById($command->id);
        $this->repository->delete($entity);
    }
}
