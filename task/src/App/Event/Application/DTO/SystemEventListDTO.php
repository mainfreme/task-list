<?php

declare(strict_types=1);

namespace App\Event\Application\DTO;

final class SystemEventListDTO
{
    /**
     * @param list<SystemEventDTO> $events
     */
    public function __construct(
        public readonly array $events,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $totalPages,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (SystemEventDTO $e) => $e->toArray(), $this->events),
            'meta' => [
                'total' => $this->total,
                'page' => $this->page,
                'per_page' => $this->perPage,
                'total_pages' => $this->totalPages,
            ],
        ];
    }
}
