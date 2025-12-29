<?php

declare(strict_types=1);

namespace Application\ApplicationManager\Command\UpdateApplicationManager;

use Application\ApplicationManager\DTO\ApplicationManagerDTO;
use Domain\ApplicationManager\Repository\ApplicationManagerRepositoryInterface;

final class UpdateApplicationManagerHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    public function handle(UpdateApplicationManagerCommand $command): ApplicationManagerDTO
    {
        $applicationManager = $this->repository->findById($command->id);

        if ($command->name !== null) {
            $applicationManager->setName($command->name);
        }

        if ($command->requestUrl !== null) {
            $applicationManager->setRequestUrl($command->requestUrl);
        }

        if ($command->isActive !== null) {
            if ($command->isActive) {
                $applicationManager->activate();
            } else {
                $applicationManager->deactivate();
            }
        }

        if ($command->ipWhitelist !== null) {
            $applicationManager->setIpWhitelist($command->ipWhitelist);
        }

        $this->repository->save($applicationManager);

        return new ApplicationManagerDTO(
            id: $applicationManager->getId(),
            name: $applicationManager->getName(),
            requestUrl: $applicationManager->getRequestUrl(),
            isActive: $applicationManager->isActive(),
            ipWhitelist: $applicationManager->getIpWhitelist(),
            createdAt: $applicationManager->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $applicationManager->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}

