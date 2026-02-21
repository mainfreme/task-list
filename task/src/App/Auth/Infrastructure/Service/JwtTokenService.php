<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Service;

use App\Auth\Application\DTO\UserDTO;
use Firebase\JWT\JWT;

final class JwtTokenService
{
    public function generateToken(UserDTO $user): string
    {
        $secret = $this->getJwtSecret();
        $algorithm = $this->getJwtAlgorithm();
        $expirationMinutes = $this->getExpirationMinutes();

        $payload = [
            'user_id' => $user->id->getValue(),
            'email' => $user->email->getValue(),
            'name' => $user->name,
            'role' => $user->role->value,
            'iat' => time(),
            'exp' => time() + ($expirationMinutes * 60),
        ];

        return JWT::encode($payload, $secret, $algorithm);
    }

    public function getExpirationMinutes(): int
    {
        return (int) env('JWT_EXPIRATION_MINUTES', 60 * 24); // Default 24 hours
    }

    private function getJwtSecret(): string
    {
        $secret = env('JWT_SECRET');

        if (!$secret) {
            throw new \RuntimeException('JWT_SECRET is not configured in .env file');
        }

        return $secret;
    }

    private function getJwtAlgorithm(): string
    {
        return env('JWT_ALGORITHM', 'HS256');
    }
}
