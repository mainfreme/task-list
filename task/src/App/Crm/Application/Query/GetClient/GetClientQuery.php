<?php

declare(strict_types=1);

namespace App\Crm\Application\Query\GetClient;

use App\Shared\Domain\ValueObject\Uuid;

final class GetClientQuery
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
