<?php

declare(strict_types=1);

namespace App\Crm\Application\Query\GetClient;

use App\Crm\Application\DTO\ClientDto;
use App\Crm\Domain\Repository\ClientRepositoryInterface;

final class GetClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    public function handle(GetClientQuery $query): ClientDto
    {
        $client = $this->repository->findById($query->id);

        return ClientDto::fromAggregate($client);
    }
}
