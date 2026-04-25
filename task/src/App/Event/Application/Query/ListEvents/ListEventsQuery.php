<?php

declare(strict_types=1);

namespace App\Event\Application\Query\ListEvents;

use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

final class ListEventsQuery
{
    /**
     * @param list<Uuid>   $userIds         puste = brak filtra
     * @param list<string> $applicationIds  puste = brak filtra
     * @param list<string> $modules         puste = brak filtra
     */
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly array $userIds = [],
        public readonly array $applicationIds = [],
        public readonly array $modules = [],
        public readonly ?DateTimeImmutable $dateFrom = null,
        public readonly ?DateTimeImmutable $dateTo = null,
        public readonly string $sortDir = 'desc',
    ) {
    }
}
