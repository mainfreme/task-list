<?php

declare(strict_types=1);

namespace App\Task\Application\Query\GetTask;

final class GetTaskQuery
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}
