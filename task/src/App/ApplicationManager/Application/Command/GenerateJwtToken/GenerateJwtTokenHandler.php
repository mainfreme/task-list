<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateJwtToken;

use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use Firebase\JWT\JWT;

final class GenerateJwtTokenHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    public function handle(GenerateJwtTokenCommand $command): string
    {
        $applicationManager = $this->repository->findById($command->uuid);

        // Check if application is active
        if (!$applicationManager->isActive()) {
            throw new \RuntimeException('Cannot generate JWT token for inactive application');
        }

        $secret = $this->getJwtSecret();
        $algorithm = $this->getJwtAlgorithm();
        $expirationMinutes = $command->expirationMinutes ?? (int) env('JWT_EXPIRATION_MINUTES', 60 * 24); // Default 24 hours

        $issuedAt = time();
        $expiration = $issuedAt + ($expirationMinutes * 60);

        $payload = [
            'application_id' => $applicationManager->getId()->getValue(),
            'application_name' => $applicationManager->getName()->getValue(),
            'iat' => $issuedAt,
            'exp' => $expiration,
        ];

        return JWT::encode($payload, $secret, $algorithm);
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
