<?php

declare(strict_types=1);

namespace App\Crm\Application\DTO;

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

    /**
     * Odtwarza DTO z tablicy w kształcie {@see self::toArray()} (np. z cache Redis).
     *
     * @param array{data: list<array<string, mixed>>, meta: array{total: int, page: int, per_page: int, total_pages: int}} $array
     */
    public static function fromArray(array $array): self
    {
        $clients = array_map(
            static fn (array $row) => ClientDto::fromArray($row),
            $array['data']
        );

        return new self(
            clients: $clients,
            total: $array['meta']['total'],
            page: $array['meta']['page'],
            perPage: $array['meta']['per_page'],
            totalPages: $array['meta']['total_pages'],
        );
    }
}
