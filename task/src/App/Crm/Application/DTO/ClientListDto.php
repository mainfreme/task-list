<?php

declare(strict_types=1);

namespace App\Crm\Application\DTO;

use App\Crm\Domain\Dto\ClientDto;

final class ClientListDto
{
    /**
     * @param ClientDto[] $clients
     */
    public function __construct(
        public readonly array $clients,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $totalPages,
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn (ClientDto $client) => $client->toArray(), $this->clients),
            'meta' => [
                'total' => $this->total,
                'page' => $this->page,
                'per_page' => $this->perPage,
                'total_pages' => $this->totalPages,
            ],
        ];
    }
}
