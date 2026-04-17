<?php

declare(strict_types=1);

namespace App\Crm\Application\Query\ListClients;

use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Application\DTO\ClientDto;
use App\Crm\Application\DTO\ClientListDto;
use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Repository\ClientRepositoryInterface;

final class ListClientsHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository,
        private readonly ListClientsQueryCacheInterface $listClientsCache,
    ) {
    }

    public function handle(ListClientsQuery $query): ClientListDto
    {
        $cached = $this->listClientsCache->find($query);
        if ($cached !== null) {
            return $cached;
        }

        $offset = ($query->page - 1) * $query->perPage;

        if ($query->status !== null) {
            $clients = $this->repository->findByStatus($query->status, $query->perPage, $offset);
            $total = $this->repository->countByStatus($query->status);
        } else {
            $clients = $this->repository->findAll($query->perPage, $offset);
            $total = $this->repository->count();
        }
        $totalPages = (int) ceil($total / $query->perPage);

        $clientDTOs = array_map(
            static fn (CrmClientAggregate $client) => ClientDto::fromAggregate($client),
            $clients
        );

        $result = new ClientListDto(
            clients: $clientDTOs,
            total: $total,
            page: $query->page,
            perPage: $query->perPage,
            totalPages: $totalPages,
        );

        $this->listClientsCache->save($query, $result);

        return $result;
    }
}
