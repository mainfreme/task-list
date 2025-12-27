<?php

declare(strict_types=1);

namespace Domain\Task\Entity;

use Domain\Task\ValueObject\TaskStatus;

final class Task
{
    private ?int $id = null;

    public function __construct(
        private string $title,
        private string $websiteUrl,
        private string $description,
        private string $phone,
        private string $email,
        private string $address,
        private TaskStatus $status = TaskStatus::PENDING,
        private ?int $applicationManagerId = null,
        private ?\DateTimeImmutable $dueDate = null,
        private ?string $deliveryAddress = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        string $title,
        string $websiteUrl,
        string $description,
        string $phone,
        string $email,
        string $address,
        ?int $applicationManagerId = null,
        ?\DateTimeImmutable $dueDate = null,
        ?string $deliveryAddress = null
    ): self {
        return new self(
            $title,
            $websiteUrl,
            $description,
            $phone,
            $email,
            $address,
            TaskStatus::PENDING,
            $applicationManagerId,
            $dueDate,
            $deliveryAddress
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->touch();
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(string $websiteUrl): void
    {
        $this->websiteUrl = $websiteUrl;
        $this->touch();
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
        $this->touch();
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
        $this->touch();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->touch();
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
        $this->touch();
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
        $this->touch();
    }

    public function getApplicationManagerId(): ?int
    {
        return $this->applicationManagerId;
    }

    public function setApplicationManagerId(?int $applicationManagerId): void
    {
        $this->applicationManagerId = $applicationManagerId;
        $this->touch();
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeImmutable $dueDate): void
    {
        $this->dueDate = $dueDate;
        $this->touch();
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?string $deliveryAddress): void
    {
        $this->deliveryAddress = $deliveryAddress;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}

