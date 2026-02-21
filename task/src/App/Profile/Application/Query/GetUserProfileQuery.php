<?php


namespace App\Profile\Application\Query;

use App\Shared\Domain\ValueObject\Uuid;

final class GetUserProfileQuery
{
    public function __construct(
        public readonly Uuid $userId,
    ) {
    }
}
