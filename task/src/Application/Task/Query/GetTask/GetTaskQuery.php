<?php

declare(strict_types=1);

namespace Application\Task\Query\GetTask;

final class GetTaskQuery
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}

