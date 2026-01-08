<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Query\ListApplicationManagers;

final class ListApplicationManagersQuery
{
    public function __construct(
        public readonly ?bool $isActive = null,
    ) {
    }
}
