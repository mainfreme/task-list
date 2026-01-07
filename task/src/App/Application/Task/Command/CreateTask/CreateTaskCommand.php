<?php

declare(strict_types=1);

namespace App\Application\Task\Command\CreateTask;

final class CreateTaskCommand
{
    public function __construct(
        public readonly string $title,
        public readonly string $websiteUrl,
        public readonly string $description,
        public readonly string $phone,
        public readonly string $email,
        public readonly string $address,
        public readonly ?int $applicationManagerId = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $deliveryAddress = null,
    ) {
    }
}

