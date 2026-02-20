<?php

declare(strict_types=1);

namespace App\Task\Application\Command\UpdateTask;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;

final class UpdateTaskCommand
{
    public function __construct(
        public readonly Uuid $id,
        public readonly ?Title $title = null,
        public readonly ?WebsiteUrl $websiteUrl = null,
        public readonly ?Description $description = null,
        public readonly ?Phone $phone = null,
        public readonly ?Email $email = null,
        public readonly ?Address $address = null,
        public readonly ?DueDate $dueDate = null,
        public readonly ?DeliveryAddress $deliveryAddress = null,
    ) {
    }
}
