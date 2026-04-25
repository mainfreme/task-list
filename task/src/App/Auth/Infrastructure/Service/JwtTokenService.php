<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Service;

use App\Auth\Domain\Service\JwtTokenServiceInterface;
use App\Auth\Domain\ValueObject\UserIdentity;
use Firebase\JWT\JWT;

final class JwtTokenService implements JwtTokenServiceInterface
{
    public function generateToken(UserIdentity $identity): string
    {
        $secret = $this->getJwtSecret();
        $algorithm = $this->getJwtAlgorithm();
        $expirationMinutes = $this->getExpirationMinutes();

        $payload = [
            'user_id' => $identity->id->getValue(),
            'email' => $identity->email->getValue(),
            'name' => $identity->name,
            'role' => $identity->role->value,
            'iat' => time(),
            'exp' => time() + ($expirationMinutes * 60),
        ];

        return JWT::encode($payload, $secret, $algorithm);
    }

    public function getExpirationMinutes(): int
    {
        return (int) config('auth_jwt.expiration_minutes', 60 * 24);
    }

    private function getJwtSecret(): string
    {
        $secret = config('auth_jwt.secret');

        if (!is_string($secret) || $secret === '') {
            throw new \RuntimeException('JWT secret is not configured (auth_jwt.secret / JWT_SECRET).');
        }

        return $secret;
    }

    private function getJwtAlgorithm(): string
    {
        return (string) config('auth_jwt.algorithm', 'HS256');
    }
}
