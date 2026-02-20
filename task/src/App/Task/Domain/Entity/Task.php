<?php

declare(strict_types=1);

namespace App\Task\Domain\Entity;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;

final class Task
{
    private ?Uuid $id = null;

    public function __construct(
        private Title $title,
        private WebsiteUrl $websiteUrl,
        private Description $description,
        private Phone $phone,
        private Email $email,
        private Address $address,
        private TaskStatus $status = TaskStatus::PENDING,
        private ?ApplicationManagerId $applicationManagerId = null,
        private ?DueDate $dueDate = null,
        private ?DeliveryAddress $deliveryAddress = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public static function create(
        Title $title,
        WebsiteUrl $websiteUrl,
        Description $description,
        Phone $phone,
        Email $email,
        Address $address,
        ?ApplicationManagerId $applicationManagerId = null,
        ?DueDate $dueDate = null,
        ?DeliveryAddress $deliveryAddress = null
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

    public static function fromDatabase(
        Title $title,
        WebsiteUrl $websiteUrl,
        Description $description,
        Phone $phone,
        Email $email,
        Address $address,
        TaskStatus $status,
        ?ApplicationManagerId $applicationManagerId = null,
        ?DueDate $dueDate = null,
        ?DeliveryAddress $deliveryAddress = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ): self {
        return new self(
            $title,
            $websiteUrl,
            $description,
            $phone,
            $email,
            $address,
            $status,
            $applicationManagerId,
            $dueDate,
            $deliveryAddress,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(Title $title): void
    {
        $this->title = $title;
        $this->touch();
    }

    public function getWebsiteUrl(): WebsiteUrl
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(WebsiteUrl $websiteUrl): void
    {
        $this->websiteUrl = $websiteUrl;
        $this->touch();
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function setDescription(Description $description): void
    {
        $this->description = $description;
        $this->touch();
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function setPhone(Phone $phone): void
    {
        $this->phone = $phone;
        $this->touch();
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
        $this->touch();
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
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

    public function getApplicationManagerId(): ?ApplicationManagerId
    {
        return $this->applicationManagerId;
    }

    public function setApplicationManagerId(?ApplicationManagerId $applicationManagerId): void
    {
        $this->applicationManagerId = $applicationManagerId;
        $this->touch();
    }

    public function getDueDate(): ?DueDate
    {
        return $this->dueDate;
    }

    public function setDueDate(?DueDate $dueDate): void
    {
        $this->dueDate = $dueDate;
        $this->touch();
    }

    public function getDeliveryAddress(): ?DeliveryAddress
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?DeliveryAddress $deliveryAddress): void
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
