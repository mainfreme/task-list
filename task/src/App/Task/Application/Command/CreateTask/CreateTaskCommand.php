<?php

declare(strict_types=1);

namespace App\Task\Application\Command\CreateTask;

use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\Phone;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Address;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Source;

#[MapFrom(Source::BODY)]
final class CreateTaskCommand
{
    public function __construct(
        #[MapField('title')]
        public readonly Title $title,
        #[MapField('website_url')]
        public readonly WebsiteUrl $websiteUrl,
        #[MapField('description')]
        public readonly Description $description,
        #[MapField('phone')]
        public readonly Phone $phone,
        #[MapField('email')]
        public readonly Email $email,
        #[MapField('address')]
        public readonly Address $address,
        #[MapFrom(Source::ATTRIBUTES)]
        #[MapField('application_manager_id')]
        public readonly ?ApplicationManagerId $applicationManagerId = null,
        #[MapField('due_date')]
        public readonly ?DueDate $dueDate = null,
        #[MapField('delivery_address')]
        public readonly ?DeliveryAddress $deliveryAddress = null,
    ) {
    }
}
