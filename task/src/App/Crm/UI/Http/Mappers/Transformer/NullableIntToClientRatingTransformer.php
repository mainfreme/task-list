<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Mappers\Transformer;

use App\Crm\Domain\ValueObject\ClientRating;
use App\Shared\Infrastructure\Mapper\Transformer\TransformerInterface;

final class NullableIntToClientRatingTransformer implements TransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        return ClientRating::fromInt((int) $value);
    }
}
