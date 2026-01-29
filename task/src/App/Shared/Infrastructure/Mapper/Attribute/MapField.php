<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class MapField
{
    public function __construct(
        public ?string $key = null,
        public ?string $transformer = null
    ) {
    }
}
