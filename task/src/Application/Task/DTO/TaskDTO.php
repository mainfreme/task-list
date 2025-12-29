<?php

declare(strict_types=1);

namespace Application\Task\DTO;

final class TaskDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $websiteUrl,
        public readonly string $description,
        public readonly string $phone,
        public readonly string $email,
        public readonly string $address,
        public readonly string $status,
        public readonly ?int $applicationManagerId = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $deliveryAddress = null,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'website_url' => $this->websiteUrl,
            'description' => $this->description,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => $this->status,
            'application_manager_id' => $this->applicationManagerId,
            'due_date' => $this->dueDate,
            'delivery_address' => $this->deliveryAddress,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

