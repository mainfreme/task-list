<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Domain\Aggregate\CrmClientAggregate;
use App\Crm\Domain\Enums\ClientStatus;
use App\Crm\Domain\Exception\ClientNotFoundException;
use App\Shared\Domain\ValueObject\Uuid;

interface ClientRepositoryInterface
{
    /**
     * @throws ClientNotFoundException
     */
    public function findById(Uuid $id): CrmClientAggregate;

    /**
     * @return CrmClientAggregate[]
     */
    public function findAll(int $limit = 50, int $offset = 0): array;

    /**
     * @return CrmClientAggregate[]
     */
    public function findByStatus(ClientStatus $status, int $limit = 50, int $offset = 0): array;

    public function count(): int;

    public function countByStatus(ClientStatus $status): int;

    public function save(CrmClientAggregate $client): void;

    /**
     * @throws ClientNotFoundException
     */
    public function softDelete(CrmClientAggregate $client): void;
}
