<?php

declare(strict_types=1);

namespace App\Task\Application\DTO;

use App\Task\Domain\ValueObject\Uuid;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\Phone;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Address;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\DeliveryAddress;

final class TaskDTO
{
    public function __construct(
        public readonly Uuid $id,
        public readonly Title $title,
        public readonly WebsiteUrl $websiteUrl,
        public readonly Description $description,
        public readonly Phone $phone,
        public readonly Email $email,
        public readonly Address $address,
        public readonly TaskStatus $status,
        public readonly ?ApplicationManagerId $applicationManagerId = null,
        public readonly ?DueDate $dueDate = null,
        public readonly ?DeliveryAddress $deliveryAddress = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'title' => $this->title->getValue(),
            'website_url' => $this->websiteUrl->getValue(),
            'description' => $this->description->getValue(),
            'phone' => $this->phone->getValue(),
            'email' => $this->email->getValue(),
            'address' => $this->address->getValue(),
            'status' => $this->status->value,
            'application_manager_id' => $this->applicationManagerId?->getValue(),
            'due_date' => $this->dueDate?->format('Y-m-d H:i:s'),
            'delivery_address' => $this->deliveryAddress?->getValue(),
        ];
    }
}
