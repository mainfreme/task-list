<?php

declare(strict_types=1);

namespace App\Event\Domain\Repository;

use App\Event\Domain\Entity\SystemEvent;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

interface SystemEventRepositoryInterface
{
    /**
     * @param list<Uuid> $userIds
     * @param list<string> $applicationIds
     * @param list<string> $modules
     * @return list<SystemEvent>
     */
    public function findForList(
        array $userIds,
        array $applicationIds,
        array $modules,
        ?DateTimeImmutable $dateFrom,
        ?DateTimeImmutable $dateTo,
        int $limit,
        int $offset,
        string $sortDir,
    ): array;

    /**
     * @param list<Uuid> $userIds
     * @param list<string> $applicationIds
     * @param list<string> $modules
     */
    public function countForList(
        array $userIds,
        array $applicationIds,
        array $modules,
        ?DateTimeImmutable $dateFrom,
        ?DateTimeImmutable $dateTo,
    ): int;

    /**
     * @return list<string>
     */
    public function distinctModules(): array;
}
