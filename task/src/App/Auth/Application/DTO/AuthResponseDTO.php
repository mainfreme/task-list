<?php

declare(strict_types=1);

namespace App\Auth\Application\DTO;

final class AuthResponseDTO
{
    public function __construct(
        public readonly UserDTO $user,
        public readonly string $token,
        public readonly string $tokenType = 'Bearer',
        public readonly int $expiresIn = 1440,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'user' => $this->user->toArray(),
            'token' => $this->token,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
        ];
    }
}
