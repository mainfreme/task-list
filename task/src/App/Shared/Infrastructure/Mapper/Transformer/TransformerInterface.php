<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper\Transformer;

interface TransformerInterface
{
    public function transform(mixed $value): mixed;
}
