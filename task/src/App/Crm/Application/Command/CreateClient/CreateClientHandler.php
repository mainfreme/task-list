<?php

declare(strict_types=1);

namespace App\Crm\Application\Command\CreateClient;

use App\Crm\Application\Cache\ListClientsQueryCacheInterface;
use App\Crm\Application\DTO\ClientDto;
use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Repository\ClientRepositoryInterface;

final class CreateClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository,
        private readonly ListClientsQueryCacheInterface $listClientsCache,
    ) {
    }

    public function handle(CreateClientCommand $command): ClientDto
    {
        $client = CrmClientAggregate::create(
            name: $command->name,
            nip: $command->nip,
            country: $command->country,
            status: $command->status,
            isCompany: $command->isCompany,
            regon: $command->regon,
            pesel: $command->pesel,
            source: $command->source,
            rating: $command->rating,
            notes: $command->notes,
            addressUuid: $command->addressUuid,
        );

        $this->repository->save($client);

        $this->listClientsCache->invalidate();

        return ClientDto::fromAggregate($client);
    }
}
