<?php

declare(strict_types=1);

namespace App\Application\ApplicationManager\Query\GetApplicationManager;

final class GetApplicationManagerQuery
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}

