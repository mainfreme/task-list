<?php

declare(strict_types=1);

namespace App\ApplicationManager\Domain\Repository;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\ValueObject\ApiKey;

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
