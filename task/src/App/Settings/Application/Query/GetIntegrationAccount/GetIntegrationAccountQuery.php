<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetIntegrationAccount;

use App\Shared\Domain\ValueObject\Uuid;

final class GetIntegrationAccountQuery
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
