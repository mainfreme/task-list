<?php

declare(strict_types=1);

namespace App\Task\Application\Command\UpdateTask;

final class UpdateTaskCommand
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $title = null,
        public readonly ?string $websiteUrl = null,
        public readonly ?string $description = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $address = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $deliveryAddress = null,
    ) {
    }
}
