<?php

declare(strict_types=1);

namespace App\Crm\Application\Query\ListClients;

use App\Crm\Domain\Enums\ClientStatus;

final class ListClientsQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?ClientStatus $status = null,
    ) {
    }
}
