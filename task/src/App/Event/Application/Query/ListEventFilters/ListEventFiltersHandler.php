<?php

declare(strict_types=1);

namespace App\Event\Application\Query\ListEventFilters;

use App\Event\Domain\Repository\SystemEventRepositoryInterface;

final class ListEventFiltersHandler
{
    public function __construct(
        private readonly SystemEventRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array{modules: list<string>}
     */
    public function handle(): array
    {
        return [
            'modules' => $this->repository->distinctModules(),
        ];
    }
}
