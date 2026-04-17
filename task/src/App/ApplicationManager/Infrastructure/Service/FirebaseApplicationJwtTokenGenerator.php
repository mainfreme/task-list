<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Service;

use App\ApplicationManager\Domain\Service\ApplicationJwtTokenGeneratorInterface;
use Firebase\JWT\JWT;
use RuntimeException;

final class FirebaseApplicationJwtTokenGenerator implements ApplicationJwtTokenGeneratorInterface
{
    public function defaultExpirationMinutes(): int
    {
        return (int) config('application_manager.jwt.default_expiration_minutes', 60 * 24);
    }

    public function generate(string $applicationId, string $applicationName, int $expirationMinutes): string
    {
        $secret = $this->secret();
        $algorithm = $this->algorithm();

        $issuedAt = time();
        $expiration = $issuedAt + ($expirationMinutes * 60);

        $payload = [
            'application_id' => $applicationId,
            'application_name' => $applicationName,
            'iat' => $issuedAt,
            'exp' => $expiration,
        ];

        return JWT::encode($payload, $secret, $algorithm);
    }

    private function secret(): string
    {
        $secret = config('application_manager.jwt.secret');

        if ($secret === null || $secret === '') {
            throw new RuntimeException('JWT secret is not configured (application_manager.jwt.secret)');
        }

        return (string) $secret;
    }

    private function algorithm(): string
    {
        return (string) config('application_manager.jwt.algorithm', 'HS256');
    }
}
