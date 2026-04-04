<?php

declare(strict_types=1);

namespace App\Ops\Application\Command\RecordDeployFailure;

use App\Ops\Domain\Entity\DeployFailure;
use App\Ops\Domain\Repository\DeployFailureRepositoryInterface;

final class RecordDeployFailureHandler
{
    public function __construct(
        private readonly DeployFailureRepositoryInterface $repository,
    ) {
    }

    public function handle(RecordDeployFailureCommand $command): string
    {
        $failure = DeployFailure::create(
            $command->project,
            $command->repository,
            $command->container,
            $command->stage,
            $command->message,
            $command->hostname,
        );

        $this->repository->save($failure);

        return $failure->getId()->getValue();
    }
}
