<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateJwtToken;

use App\ApplicationManager\Application\DTO\GeneratedApplicationJwtTokenDTO;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\Service\ApplicationJwtTokenGeneratorInterface;
use RuntimeException;

final class GenerateJwtTokenHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository,
        private readonly ApplicationJwtTokenGeneratorInterface $jwtTokenGenerator,
    ) {
    }

    public function handle(GenerateJwtTokenCommand $command): GeneratedApplicationJwtTokenDTO
    {
        $applicationManager = $this->repository->findById($command->uuid);
        $applicationManager->assertMayGenerateAccessToken();

        $id = $applicationManager->getId();
        if ($id === null) {
            throw new RuntimeException('ApplicationManager must have an identifier');
        }

        $expirationMinutes = $command->expirationMinutes ?? $this->jwtTokenGenerator->defaultExpirationMinutes();

        $token = $this->jwtTokenGenerator->generate(
            $id->getValue(),
            $applicationManager->getName()->getValue(),
            $expirationMinutes
        );

        return new GeneratedApplicationJwtTokenDTO(
            token: $token,
            expiresInMinutes: $expirationMinutes,
        );
    }
}
