<?php

declare(strict_types=1);

namespace Application\ApplicationManager\Query\ListApplicationManagers;

final class ListApplicationManagersQuery
{
    public function __construct(
        public readonly ?bool $isActive = null,
    ) {
    }
}

