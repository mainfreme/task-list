<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\GetChartDefinition;

use App\Shared\Domain\ValueObject\Uuid;

final class GetChartDefinitionQuery
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
