<?php

declare(strict_types=1);

namespace App\Crm\Application\Command\CreateClient;

use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Dto\ClientDto;
use App\Crm\Domain\Repository\ClientRepositoryInterface;

final class CreateClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    public function handle(CreateClientCommand $command): ClientDto
    {
        $dto = new ClientDto(
            name: $command->name,
            nip: $command->nip,
            country: $command->country,
            isCompany: $command->isCompany,
            regon: $command->regon,
            pesel: $command->pesel,
            source: $command->source,
            rating: $command->rating,
            notes: $command->notes,
            status: $command->status,
            addressUuid: $command->addressUuid,
        );

        $client = CrmClientAggregate::create($dto);

        $this->repository->save($client);

        return $this->toDTO($client);
    }

    private function toDTO(CrmClientAggregate $client): ClientDto
    {
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
