<?php

declare(strict_types=1);

namespace Application\ApplicationManager\Command\GenerateApiKey;

use Application\ApplicationManager\DTO\ApplicationManagerDTO;
use Domain\ApplicationManager\Repository\ApplicationManagerRepositoryInterface;
use Domain\ApplicationManager\ValueObject\ApiKey;

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

        return new ApplicationManagerDTO(
            id: $applicationManager->getId(),
            name: $applicationManager->getName(),
            apiKey: $newApiKey->value(), // Return plain key only when regenerating
            requestUrl: $applicationManager->getRequestUrl(),
            isActive: $applicationManager->isActive(),
            ipWhitelist: $applicationManager->getIpWhitelist(),
            createdAt: $applicationManager->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $applicationManager->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}

