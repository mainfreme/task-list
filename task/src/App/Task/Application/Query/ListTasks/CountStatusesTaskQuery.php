<?php

declare(strict_types=1);

namespace App\Task\Application\Query\ListTasks;

use App\Task\Domain\ValueObject\ApplicationManagerId;

final class CountStatusesTaskQuery
{
    public function __construct(
        public readonly ?string $site = null,
        public readonly ?string $status = null,
        public readonly ?ApplicationManagerId $applicationManagerId = null
    ) {
    }
}
