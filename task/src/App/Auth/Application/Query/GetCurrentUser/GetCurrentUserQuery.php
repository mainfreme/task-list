<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetCurrentUser;

use App\Shared\Domain\ValueObject\Uuid;

final class GetCurrentUserQuery
{
    public function __construct(
        public readonly Uuid $userId,
    ) {
    }
}
