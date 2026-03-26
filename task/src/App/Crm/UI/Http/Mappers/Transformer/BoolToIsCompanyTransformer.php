<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Mappers\Transformer;

use App\Crm\Domain\ValueObject\IsCompany;
use App\Shared\Infrastructure\Mapper\Transformer\TransformerInterface;

final class BoolToIsCompanyTransformer implements TransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if ($value === null) {
            return new IsCompany(false);
        }

        return IsCompany::fromBool((bool) $value);
    }
}
