<?php

declare(strict_types=1);

namespace App\Crm\Application\Command\UpdateClient;

use App\Crm\Domain\Dto\ClientDto;
use App\Crm\Domain\Repository\ClientRepositoryInterface;

final class UpdateClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    public function handle(UpdateClientCommand $command): ClientDto
    {
        $client = $this->repository->findById($command->id);

        foreach ($command->clearFields as $field) {
            if ($field === 'address_uuid') {
                $client->setAddressUuid(null);
            }
        }

        if ($command->name !== null) {
            $client->setName($command->name);
        }

        if ($command->nip !== null) {
            $client->setNip($command->nip);
        }

        if ($command->country !== null) {
            $client->setCountry($command->country);
        }

        if ($command->status !== null) {
            $client->setStatus($command->status);
        }

        if ($command->isCompany !== null) {
            $client->setIsCompany($command->isCompany);
        }

        if ($command->regon !== null) {
            $client->setRegon($command->regon);
        }

        if ($command->pesel !== null) {
            $client->setPesel($command->pesel);
        }

        if ($command->source !== null) {
            $client->setSource($command->source);
        }

        if ($command->rating !== null) {
            $client->setRating($command->rating);
        }

        if ($command->notes !== null) {
            $client->setNotes($command->notes);
        }

        if ($command->addressUuid !== null) {
            $client->setAddressUuid($command->addressUuid);
        }

        $this->repository->save($client);

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
