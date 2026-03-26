<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\DeleteSettingEntry;

use App\Settings\Domain\Repository\SettingEntryRepositoryInterface;

final class DeleteSettingEntryHandler
{
    public function __construct(
        private readonly SettingEntryRepositoryInterface $repository,
    ) {
    }

    public function handle(DeleteSettingEntryCommand $command): void
    {
        $entity = $this->repository->findById($command->id);
        $this->repository->delete($entity);
    }
}
