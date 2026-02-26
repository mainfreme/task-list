<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Domain\DTO\Stats\CountStatusesTaskDto;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Exception\TaskNotFoundException;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use App\Task\Infrastructure\Model\TaskModel;
use Illuminate\Support\Facades\DB;

final class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function findById(Uuid $id): Task
    {
        $model = TaskModel::find($id->getValue());

        if (!$model) {
            throw TaskNotFoundException::byId($id->getValue());
        }

        return $this->toEntity($model);
    }

    public function findByApplicationId(ApplicationManagerId $applicationId): array
    {
        return TaskModel::where('application_manager_id', $applicationId->getValue())
            ->get()
            ->map(fn (TaskModel $model) => $this->toEntity($model))
            ->toArray();
    }

    public function findByStatus(TaskStatus $status): array
    {
        return TaskModel::where('status', $status->value)
            ->get()
            ->map(fn (TaskModel $model) => $this->toEntity($model))
            ->toArray();
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return TaskModel::limit($limit)
            ->offset($offset)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (TaskModel $model) => $this->toEntity($model))
            ->toArray();
    }

    public function count(): int
    {
        return TaskModel::count();
    }

    public function save(Task $task): void
    {
        $data = [
            'title' => $task->getTitle()->getValue(),
            'website_url' => $task->getWebsiteUrl()->getValue(),
            'description' => $task->getDescription()->getValue(),
            'phone' => $task->getPhone()->getValue(),
            'email' => $task->getEmail()->getValue(),
            'address' => $task->getAddress()->getValue(),
            'status' => $task->getStatus()->value,
            'application_manager_id' => $task->getApplicationManagerId()?->getValue(),
            'due_date' => $task->getDueDate()?->format('Y-m-d H:i:s'),
            'delivery_address' => $task->getDeliveryAddress()?->getValue(),
        ];

        if ($task->getId() === null) {
            $model = TaskModel::create($data);
            $task->setId(Uuid::fromString($model->id));
        } else {
            TaskModel::where('id', $task->getId()->getValue())->update($data);
        }
    }

    public function softDelete(Task $task): void
    {
        $model = TaskModel::find($task->getId()->getValue());

        if (!$model) {
            throw TaskNotFoundException::byId($task->getId()->getValue());
        }

        TaskModel::where('id', $task->getId()->getValue())->update(['deleted_at' => now()]);
    }

    public function delete(Task $task): void
    {
        if ($task->getId() !== null) {
            TaskModel::destroy($task->getId()->getValue());
        }
    }

    private function toEntity(TaskModel $model): Task
    {
        $entity = Task::fromDatabase(
            Title::fromString($model->title),
            WebsiteUrl::fromString($model->website_url),
            Description::fromString($model->description),
            Phone::fromString($model->phone),
            Email::fromString($model->email),
            Address::fromString($model->address),
            TaskStatus::fromString($model->status),
            ApplicationManagerId::fromNullable($model->application_manager_id),
            DueDate::fromNullable($model->due_date ? $model->due_date->format('Y-m-d H:i:s') : null),
            DeliveryAddress::fromNullable($model->delivery_address),
            $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );

        $entity->setId(Uuid::fromString($model->id));

        return $entity;
    }

    public function groupByStatus(CountStatusesTaskDto $dto): array
    {
        return TaskModel::select('status', DB::raw('COUNT(*) as count'))
            ->when($dto->applicationManagerId, fn ($query) => $query->where('application_manager_id', $dto->applicationManagerId->getValue()))
            ->when($dto->site, fn ($query) => $query->where('website_url', $dto->site))
            ->when($dto->status, fn ($query) => $query->where('status', $dto->status))
            ->groupBy('status')
            ->get()
            ->map(fn (TaskModel $model) => [
                'status' => TaskStatus::fromString($model->status),
                'count' => $model->count,
            ])
            ->toArray();
    }
}
