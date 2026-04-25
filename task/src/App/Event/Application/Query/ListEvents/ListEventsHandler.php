<?php

declare(strict_types=1);

namespace App\Event\Application\Query\ListEvents;

use App\Event\Application\DTO\SystemEventDTO;
use App\Event\Application\DTO\SystemEventListDTO;
use App\Event\Domain\Entity\SystemEvent;
use App\Event\Domain\Repository\SystemEventRepositoryInterface;

final class ListEventsHandler
{
    public function __construct(
        private readonly SystemEventRepositoryInterface $repository,
    ) {
    }

    public function handle(ListEventsQuery $query): SystemEventListDTO
    {
        $page = max(1, $query->page);
        $perPage = max(1, min(200, $query->perPage));
        $offset = ($page - 1) * $perPage;

        $events = $this->repository->findForList(
            $query->userIds,
            $query->applicationIds,
            $query->modules,
            $query->dateFrom,
            $query->dateTo,
            $perPage,
            $offset,
            $query->sortDir,
        );

        $total = $this->repository->countForList(
            $query->userIds,
            $query->applicationIds,
            $query->modules,
            $query->dateFrom,
            $query->dateTo,
        );

        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;

        $dtos = array_map(
            static fn (SystemEvent $event) => SystemEventDTO::fromEntity($event),
            $events,
        );

        return new SystemEventListDTO(
            events: $dtos,
            total: $total,
            page: $page,
            perPage: $perPage,
            totalPages: $totalPages,
        );
    }
}
