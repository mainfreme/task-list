<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\UpdateApplicationManager;

use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;

final class ChangeStatusHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    public function handle(ChangeStatusCommand $command): void
    {
        $applicationManager = $this->repository->findById($command->uuid);
        if ($command->isActive) {
            $applicationManager->activate();
        } else {
            $applicationManager->deactivate();
        }
        $this->repository->save($applicationManager);
    }
}
