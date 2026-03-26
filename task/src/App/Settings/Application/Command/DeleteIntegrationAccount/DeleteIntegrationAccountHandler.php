<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteIntegrationAccount;

use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;

final class DeleteIntegrationAccountHandler
{
    public function __construct(
        private readonly IntegrationAccountRepositoryInterface $repository,
    ) {
    }

    public function handle(DeleteIntegrationAccountCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
