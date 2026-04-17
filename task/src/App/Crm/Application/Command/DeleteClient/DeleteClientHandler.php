<?php

declare(strict_types=1);

namespace App\Crm\Application\Command\DeleteClient;

use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Domain\Repository\ClientRepositoryInterface;

final class DeleteClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository,
        private readonly ListClientsQueryCacheInterface $listClientsCache,
    ) {
    }

    public function handle(DeleteClientCommand $command): void
    {
        $client = $this->repository->findById($command->id);
        $this->repository->softDelete($client);

        $this->listClientsCache->invalidate();
    }
}
