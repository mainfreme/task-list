<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\DTO;

final readonly class GeneratedApplicationJwtTokenDTO
{
    public function __construct(
        public string $token,
        public int $expiresInMinutes,
        public string $tokenType = 'Bearer',
    ) {
    }

    /**
     * @return array{token: string, token_type: string, expires_in: int}
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresInMinutes,
        ];
    }
}
