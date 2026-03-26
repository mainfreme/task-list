<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Domain\Entity\Internal\Address;
use App\Shared\Domain\ValueObject\Uuid;

interface AddressRepositoryInterface
{
    /**
     * @return Address|null
     */
    public function findById(Uuid $id): ?Address;

    /**
     * @return Address[]
     */
    public function findByClientUuid(Uuid $clientUuid): array;

    public function save(Address $address): void;

    public function delete(Address $address): void;
}
