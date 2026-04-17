<?php

declare(strict_types=1);

namespace App\Crm\Application\Cache;

use App\Crm\Application\DTO\ClientListDto;
use App\Crm\Application\Query\ListClients\ListClientsQuery;

interface ListClientsQueryCacheInterface
{
    public function find(ListClientsQuery $query): ?ClientListDto;

    public function save(ListClientsQuery $query, ClientListDto $dto): void;

    /**
     * Unieważnia wszystkie zapisane warianty listy (np. po utworzeniu / aktualizacji / usunięciu klienta).
     */
    public function invalidate(): void;
}
