<?php

declare(strict_types=1);

namespace App\Infrastructure\Task\Repository;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Infrastructure\Task\Eloquent\TaskModel;

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

    public function delete(Task $task): void
    {
        if ($task->getId() !== null) {
            TaskModel::destroy($task->getId());
        }
    }

    private function toEntity(TaskModel $model): Task
    {
        $entity = Task::create(
            $model->title,
            $model->website_url,
            $model->description,
            $model->phone,
            $model->email,
            $model->address,
            $model->application_manager_id,
            $model->due_date ? \DateTimeImmutable::createFromMutable($model->due_date) : null,
            $model->delivery_address
        );

        $entity->setId($model->id);
        $entity->setStatus(TaskStatus::fromString($model->status));

        // Set timestamps
        $reflection = new \ReflectionClass($entity);
        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($entity, \DateTimeImmutable::createFromMutable($model->created_at));

        $updatedAtProperty = $reflection->getProperty('updatedAt');
        $updatedAtProperty->setAccessible(true);
        $updatedAtProperty->setValue($entity, \DateTimeImmutable::createFromMutable($model->updated_at));

        return $entity;
    }
}

