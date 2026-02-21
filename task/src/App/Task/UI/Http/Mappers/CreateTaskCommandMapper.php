<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Mappers;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Task\Application\Command\CreateTask\CreateTaskCommand;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use App\Task\UI\Http\Requests\V1\CreateTaskRequest;

#[MapFrom(CreateTaskRequest::class)]
final class CreateTaskCommandMapper
{
    #[MapField]
    public Title $title;

    #[MapField('website_url')]
    public WebsiteUrl $websiteUrl;

    #[MapField]
    public Description $description;

    #[MapField]
    public Phone $phone;

    #[MapField]
    public Email $email;

    #[MapField]
    public Address $address;

    #[MapField('application_manager_id')]
    public ?ApplicationManagerId $applicationManagerId = null;

    #[MapField('due_date')]
    public ?DueDate $dueDate = null;

    #[MapField('delivery_address')]
    public ?DeliveryAddress $deliveryAddress = null;

    public function toCommand(): CreateTaskCommand
    {
        return new CreateTaskCommand(
            title: $this->title,
            websiteUrl: $this->websiteUrl,
            description: $this->description,
            phone: $this->phone,
            email: $this->email,
            address: $this->address,
            applicationManagerId: $this->applicationManagerId,
            dueDate: $this->dueDate,
            deliveryAddress: $this->deliveryAddress,
        );
    }
}
