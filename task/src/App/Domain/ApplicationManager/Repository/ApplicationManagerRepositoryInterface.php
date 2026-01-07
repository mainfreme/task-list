<?php

declare(strict_types=1);

namespace App\Domain\ApplicationManager\Repository;

use App\Domain\ApplicationManager\Entity\ApplicationManager;
use App\Domain\ApplicationManager\Exception\ApplicationManagerNotFoundException;
use App\Domain\ApplicationManager\ValueObject\ApiKey;

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

