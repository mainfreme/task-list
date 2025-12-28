<?php

declare(strict_types=1);

namespace Domain\ApplicationManager\Repository;

use Domain\ApplicationManager\Entity\ApplicationManager;
use Domain\ApplicationManager\Exception\ApplicationManagerNotFoundException;
use Domain\ApplicationManager\ValueObject\ApiKey;

interface ApplicationManagerRepositoryInterface
{
    /**
     * @throws ApplicationManagerNotFoundException
     */
    public function findById(int $id): ApplicationManager;

    /**
     * @throws ApplicationManagerNotFoundException
     */
    public function findByApiKey(ApiKey $apiKey): ApplicationManager;

    public function save(ApplicationManager $applicationManager): void;

    public function delete(ApplicationManager $applicationManager): void;

    /**
     * @return ApplicationManager[]
     */
    public function findAll(): array;
}

