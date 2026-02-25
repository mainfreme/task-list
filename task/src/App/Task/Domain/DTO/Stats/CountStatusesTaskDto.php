<?php

declare(strict_types=1);

namespace App\Task\Domain\DTO\Stats;

use App\Task\Domain\ValueObject\ApplicationManagerId;

final class CountStatusesTaskDto
{
    public function __construct(
        public readonly ?string $site = null,
        public readonly ?string $status = null,
        public readonly ?ApplicationManagerId $applicationManagerId = null
    ) {
    }
}