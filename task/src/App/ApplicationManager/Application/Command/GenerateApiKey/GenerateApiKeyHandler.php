<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateApiKey;

use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;

final class GenerateApiKeyHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    public function handle(GenerateApiKeyCommand $command): ApplicationManagerDTO
    {
        $applicationManager = $this->repository->findById($command->id);

        $newApiKey = ApiKey::generate();
        $applicationManager->setApiKey($newApiKey);

        $this->repository->save($applicationManager);

        $requestUrl = $applicationManager->getRequestUrl();
        $ipWhitelist = $applicationManager->getIpWhitelist();

        return new ApplicationManagerDTO(
            id: $applicationManager->getId()->getValue(),
            name: $applicationManager->getName()->getValue(),
            apiKey: $newApiKey->value(), // Return plain key only when regenerating
            requestUrl: $requestUrl?->getValue(),
            isActive: $applicationManager->isActive(),
            ipWhitelist: $ipWhitelist?->toArray(),
            createdAt: $applicationManager->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $applicationManager->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
