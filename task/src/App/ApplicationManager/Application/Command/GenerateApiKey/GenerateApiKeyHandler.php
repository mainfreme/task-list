<?php

declare(strict_types=1);

namespace App\ApplicationManager\Application\Command\GenerateApiKey;

use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
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
        $newApiKey = ApiKey::generate();

        try {
            $applicationManager = $this->repository->findById($command->id);
            $applicationManager->setApiKey($newApiKey);

            $this->repository->save($applicationManager);
        } catch (ApplicationManagerNotFoundException $e) {
            throw new ApplicationManagerNotFoundException($e->getMessage());
        }

        $requestUrl = $applicationManager->getRequestUrl();
        $ipWhitelist = $applicationManager->getIpWhitelist();

        return new ApplicationManagerDTO(
            id: $applicationManager->getId(),
            name: $applicationManager->getName(),
            apiKeyHash: $newApiKey,
            requestUrl: $requestUrl,
            isActive: $applicationManager->isActive(),
            ipWhitelist: $ipWhitelist
        );
    }
}
