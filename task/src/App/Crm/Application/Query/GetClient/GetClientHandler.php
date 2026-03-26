<?php

declare(strict_types=1);

namespace App\Crm\Application\Query\GetClient;

use App\Crm\Domain\Dto\ClientDto;
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

        return new ClientDto(
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
        );
    }
}
