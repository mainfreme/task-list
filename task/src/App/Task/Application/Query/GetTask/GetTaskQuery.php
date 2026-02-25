<?php

declare(strict_types=1);

namespace App\Task\Application\Query\GetTask;

use App\Shared\Domain\ValueObject\Uuid;

final class GetTaskQuery
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
