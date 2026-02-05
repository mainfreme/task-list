<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Query\GetApplicationManager;

use App\Shared\Domain\ValueObject\Uuid;

final class GetApplicationManagerQuery
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
