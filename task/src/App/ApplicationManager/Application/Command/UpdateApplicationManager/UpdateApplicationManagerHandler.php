<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\UpdateApplicationManager;

use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;

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

        $requestUrl = $applicationManager->getRequestUrl();
        $ipWhitelist = $applicationManager->getIpWhitelist();

        return new ApplicationManagerDTO(
            id: $applicationManager->getId(),
            name: $applicationManager->getName(),
            requestUrl: $requestUrl,
            isActive: $applicationManager->isActive(),
            ipWhitelist: $ipWhitelist
        );
    }
}
