<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Mappers;

use App\Shared\Infrastructure\Mapper\Attribute\MapField;
use App\Shared\Infrastructure\Mapper\Attribute\MapFrom;
use App\Task\Application\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Domain\ValueObject\Uuid;
use Illuminate\Http\Request;

#[MapFrom(Request::class)]
final class UpdateTaskStatusCommandMapper
{
    #[MapField]
    public string $id;

    #[MapField]
    public TaskStatus $status;

    public function toCommand(): UpdateTaskStatusCommand
    {
        return new UpdateTaskStatusCommand(
            id: Uuid::fromString($this->id),
            status: $this->status,
        );
    }
}
