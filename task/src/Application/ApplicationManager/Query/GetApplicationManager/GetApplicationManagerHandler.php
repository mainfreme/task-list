<?php

declare(strict_types=1);

namespace Application\ApplicationManager\Query\GetApplicationManager;

use Application\ApplicationManager\DTO\ApplicationManagerDTO;
use Domain\ApplicationManager\Repository\ApplicationManagerRepositoryInterface;

final class GetApplicationManagerHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    public function handle(GetApplicationManagerQuery $query): ApplicationManagerDTO
    {
        $applicationManager = $this->repository->findById($query->id);

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

