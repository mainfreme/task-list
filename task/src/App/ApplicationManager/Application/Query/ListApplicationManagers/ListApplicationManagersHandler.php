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
            function ($applicationManager): ApplicationManagerDTO {
                $requestUrl = $applicationManager->getRequestUrl();
                $ipWhitelist = $applicationManager->getIpWhitelist();

                return new ApplicationManagerDTO(
                    id: $applicationManager->getId(),
                    name: $applicationManager->getName(),
                    requestUrl: $requestUrl,
                    isActive: $applicationManager->isActive(),
                    ipWhitelist: $ipWhitelist,
                );
            },
            $applicationManagers
        );
    }
}
