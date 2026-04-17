<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Mappers;

use App\ApplicationManager\Application\Command\GenerateJwtToken\GenerateJwtTokenCommand;
use App\ApplicationManager\UI\Http\Requests\V1\GenerateJwtTokenRequest;
use App\Shared\Domain\ValueObject\Uuid;

final class GenerateJwtTokenCommandMapper
{
    public static function toCommand(GenerateJwtTokenRequest $request, string $id): GenerateJwtTokenCommand
    {
        $validated = $request->validated();

        return new GenerateJwtTokenCommand(
            uuid: Uuid::fromString($id),
            expirationMinutes: isset($validated['expiration_minutes']) ? (int) $validated['expiration_minutes'] : null,
        );
    }
}
