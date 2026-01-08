<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\CreateApplicationManager;

use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;

final class CreateApplicationManagerHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    public function handle(CreateApplicationManagerCommand $command): ApplicationManagerDTO
    {
        $apiKey = ApiKey::generate();

        $applicationManager = ApplicationManager::create(
            $command->name,
            $apiKey,
            $command->requestUrl,
            $command->isActive,
            $command->ipWhitelist
        );

        $this->repository->save($applicationManager);

        return new ApplicationManagerDTO(
            id: $applicationManager->getId(),
            name: $applicationManager->getName(),
            apiKey: $apiKey->value(), // Return plain key only on creation
            requestUrl: $applicationManager->getRequestUrl(),
            isActive: $applicationManager->isActive(),
            ipWhitelist: $applicationManager->getIpWhitelist(),
            createdAt: $applicationManager->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $applicationManager->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
