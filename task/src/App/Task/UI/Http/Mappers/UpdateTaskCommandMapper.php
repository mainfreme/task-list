<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Mappers;

use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Task\Application\Command\UpdateTask\UpdateTaskCommand;
use App\Task\Domain\ValueObject\Address;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Phone;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\Uuid;
use App\Task\Domain\ValueObject\WebsiteUrl;
use App\Task\UI\Http\Requests\V1\UpdateTaskRequest;

#[MapFrom(UpdateTaskRequest::class)]
final class UpdateTaskCommandMapper
{
    #[MapField]
    public string $id;

    #[MapField]
    public ?Title $title = null;

    #[MapField('website_url')]
    public ?WebsiteUrl $websiteUrl = null;

    #[MapField]
    public ?Description $description = null;

    #[MapField]
    public ?Phone $phone = null;

    #[MapField]
    public ?Email $email = null;

    #[MapField]
    public ?Address $address = null;

    #[MapField('due_date')]
    public ?DueDate $dueDate = null;

    #[MapField('delivery_address')]
    public ?DeliveryAddress $deliveryAddress = null;

    public function toCommand(): UpdateTaskCommand
    {
        return new UpdateTaskCommand(
            id: Uuid::fromString($this->id),
            title: $this->title,
            websiteUrl: $this->websiteUrl,
            description: $this->description,
            phone: $this->phone,
            email: $this->email,
            address: $this->address,
            dueDate: $this->dueDate,
            deliveryAddress: $this->deliveryAddress,
        );
    }
}
