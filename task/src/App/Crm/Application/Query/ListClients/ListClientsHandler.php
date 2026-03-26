<?php

declare(strict_types=1);

namespace App\Crm\Application\Query\ListClients;

use App\Crm\Application\DTO\ClientListDto;
use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Dto\ClientDto;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Repository\ClientRepositoryInterface;

final class ListClientsHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    public function handle(ListClientsQuery $query): ClientListDto
    {
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
            fn (CrmClientAggregate $client) => new ClientDto(
                name: $client->getName(),
                nip: $client->getNip(),
                country: $client->getCountry(),
                isCompany: $client->getIsCompany(),
                id: $client->getId(),
                regon: $client->getRegon(),
                pesel: $client->getPesel(),
                source: $client->getSource(),
                rating: $client->getRating(),
                notes: $client->getNotes(),
                status: $client->getStatus(),
                addressUuid: $client->getAddressUuid(),
                lastContactedAt: $client->getLastContactedAt(),
                nextContactAt: $client->getNextContactAt(),
                createdAt: $client->getCreatedAt(),
                updatedAt: $client->getUpdatedAt(),
            ),
            $clients
        );

        return new ClientListDto(
            clients: $clientDTOs,
            total: $total,
            page: $query->page,
            perPage: $query->perPage,
            totalPages: $totalPages,
        );
    }
}
