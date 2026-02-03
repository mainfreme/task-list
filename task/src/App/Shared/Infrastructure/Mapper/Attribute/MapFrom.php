<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class MapFrom
{
    /**
     * @param string $sourceClass The source class (e.g. Request::class)
     */
    public function __construct(
        public string $sourceClass
    ) {
    }
}
