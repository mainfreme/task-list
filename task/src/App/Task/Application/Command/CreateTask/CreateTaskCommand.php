<?php

declare(strict_types=1);

namespace App\Task\Application\Command\CreateTask;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;

final class CreateTaskCommand
{
    public function __construct(
        public readonly Title $title,
        public readonly WebsiteUrl $websiteUrl,
        public readonly Description $description,
        public readonly Phone $phone,
        public readonly Email $email,
        public readonly Address $address,
        public readonly ?ApplicationManagerId $applicationManagerId = null,
        public readonly ?DueDate $dueDate = null,
        public readonly ?DeliveryAddress $deliveryAddress = null,
    ) {
    }
}
