<?php

declare(strict_types=1);

namespace App\Settings\Domain\Repository;

use App\Settings\Domain\Entity\IntegrationAccount;
use App\Shared\Domain\ValueObject\Uuid;

interface IntegrationAccountRepositoryInterface
{
    public function findById(Uuid $id): IntegrationAccount;

    /**
     * @return IntegrationAccount[]
     */
    public function findAll(): array;

    public function save(IntegrationAccount $account): void;

    public function softDelete(Uuid $id): void;
}
