<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Query\GetApplicationManager;

use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;

final class GetApplicationManagerHandler
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    public function handle(GetApplicationManagerQuery $query): ApplicationManagerDTO
    {
        $applicationManager = $this->repository->findById($query->id);
        $requestUrl = $applicationManager->getRequestUrl();
        $ipWhitelist = $applicationManager->getIpWhitelist();

        return new ApplicationManagerDTO(
            id: $applicationManager->getId(),
            name: $applicationManager->getName(),
            requestUrl: $requestUrl,
            apiKeyHash: $applicationManager->getApiKey(),
            isActive: $applicationManager->isActive(),
            ipWhitelist: $ipWhitelist,
        );
    }
}
