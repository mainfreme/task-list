<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Query\ListApplicationManagers;

use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;

final class ListApplicationManagersHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    /**
     * @return ApplicationManagerDTO[]
     */
    public function handle(ListApplicationManagersQuery $query): array
    {
        $applicationManagers = $this->repository->findAll();

        if ($query->isActive !== null) {
            $applicationManagers = array_filter(
                $applicationManagers,
                fn ($am) => $am->isActive() === $query->isActive
            );
        }

        return array_map(
            fn ($applicationManager) => new ApplicationManagerDTO(
                id: $applicationManager->getId(),
                name: $applicationManager->getName(),
                requestUrl: $applicationManager->getRequestUrl(),
                isActive: $applicationManager->isActive(),
                ipWhitelist: $applicationManager->getIpWhitelist(),
                createdAt: $applicationManager->getCreatedAt()->format('Y-m-d H:i:s'),
                updatedAt: $applicationManager->getUpdatedAt()->format('Y-m-d H:i:s'),
            ),
            $applicationManagers
        );
    }
}
