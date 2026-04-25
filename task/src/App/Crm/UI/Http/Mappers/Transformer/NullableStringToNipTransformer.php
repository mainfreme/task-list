<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Mappers\Transformer;

use App\Crm\Domain\ValueObject\Nip;
use App\Shared\Infrastructure\Mapper\Transformer\TransformerInterface;

/** Puste / null → brak NIP (np. osoba prywatna). */
final class NullableStringToNipTransformer implements TransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Nip::tryFrom((string) $value);
    }
}
