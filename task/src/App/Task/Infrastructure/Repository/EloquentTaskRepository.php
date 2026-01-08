<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repository;

use App\Task\Domain\Entity\Task;
use App\Task\Domain\Exception\TaskNotFoundException;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Infrastructure\Eloquent\TaskModel;

final class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function findById(int $id): Task
    {
        $model = TaskModel::find($id);

        if (!$model) {
            throw TaskNotFoundException::byId($id);
        }

        return $this->toEntity($model);
    }

    public function findByApplicationId(int $applicationId): array
    {
        return TaskModel::where('application_manager_id', $applicationId)
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
            'title' => $task->getTitle(),
            'website_url' => $task->getWebsiteUrl(),
            'description' => $task->getDescription(),
            'phone' => $task->getPhone(),
            'email' => $task->getEmail(),
            'address' => $task->getAddress(),
            'status' => $task->getStatus()->value,
            'application_manager_id' => $task->getApplicationManagerId(),
            'due_date' => $task->getDueDate()?->format('Y-m-d H:i:s'),
            'delivery_address' => $task->getDeliveryAddress(),
        ];

        if ($task->getId() === null) {
            $model = TaskModel::create($data);
            $task->setId($model->id);
        } else {
            TaskModel::where('id', $task->getId())->update($data);
        }
    }

    public function softDelete(Task $task): void
    {
        $model = TaskModel::find($task->getId());

        if (!$model) {
            throw TaskNotFoundException::byId($task->getId());
        }

        TaskModel::where('id', $task->getId())->update(['deleted_at' => now()]);
    }

    public function delete(Task $task): void
    {
        if ($task->getId() !== null) {
            TaskModel::destroy($task->getId());
        }
    }

    private function toEntity(TaskModel $model): Task
    {
        $entity = Task::fromDatabase(
            $model->title,
            $model->website_url,
            $model->description,
            $model->phone,
            $model->email,
            $model->address,
            TaskStatus::fromString($model->status),
            $model->application_manager_id,
            $model->due_date ? \DateTimeImmutable::createFromMutable($model->due_date) : null,
            $model->delivery_address,
            $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null,
            $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null
        );

        $entity->setId($model->id);

        return $entity;
    }
}
