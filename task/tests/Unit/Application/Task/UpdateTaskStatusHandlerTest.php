<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Task;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Application\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use App\Task\Application\Command\UpdateTaskStatus\UpdateTaskStatusHandler;
use App\Task\Application\DTO\TaskDTO;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Exception\TaskNotFoundException;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use Mockery;
use PHPUnit\Framework\TestCase;

final class UpdateTaskStatusHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_when_task_not_found(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(TaskNotFoundException::byId($uuid->getValue()));
        $repository->shouldNotReceive('save');

        $handler = new UpdateTaskStatusHandler($repository);
        $command = new UpdateTaskStatusCommand($uuid, TaskStatus::COMPLETED);

        $this->expectException(TaskNotFoundException::class);

        $handler->handle($command);
    }

    public function test_handle_updates_status_saves_and_returns_dto(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $task = $this->createTask($uuid, TaskStatus::PENDING);

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($task);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (Task $t) => $t->getStatus() === TaskStatus::CANCELLED));

        $handler = new UpdateTaskStatusHandler($repository);
        $command = new UpdateTaskStatusCommand($uuid, TaskStatus::CANCELLED);

        $result = $handler->handle($command);

        $this->assertInstanceOf(TaskDTO::class, $result);
        $this->assertSame(TaskStatus::CANCELLED, $result->status);
        $this->assertSame('cancelled', $result->status->value);
    }

    public function test_handle_accepts_pending_to_in_progress_transition(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $task = $this->createTask($uuid, TaskStatus::PENDING);

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($task);
        $repository->shouldReceive('save')->once()->with(Mockery::on(fn (Task $t) => $t->getStatus() === TaskStatus::IN_PROGRESS));

        $handler = new UpdateTaskStatusHandler($repository);
        $command = new UpdateTaskStatusCommand($uuid, TaskStatus::IN_PROGRESS);

        $result = $handler->handle($command);

        $this->assertSame(TaskStatus::IN_PROGRESS, $result->status);
    }

    public function test_handle_accepts_in_progress_to_completed_transition(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $task = $this->createTask($uuid, TaskStatus::IN_PROGRESS);

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($task);
        $repository->shouldReceive('save')->once()->with(Mockery::on(fn (Task $t) => $t->getStatus() === TaskStatus::COMPLETED));

        $handler = new UpdateTaskStatusHandler($repository);
        $command = new UpdateTaskStatusCommand($uuid, TaskStatus::COMPLETED);

        $result = $handler->handle($command);

        $this->assertSame(TaskStatus::COMPLETED, $result->status);
    }

    /** Przypadek brzegowy: ustawienie tego samego statusu (idempotencja) – handler i tak wywołuje save */
    public function test_handle_still_saves_when_status_unchanged(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $task = $this->createTask($uuid, TaskStatus::COMPLETED);

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($task);
        $repository->shouldReceive('save')->once();

        $handler = new UpdateTaskStatusHandler($repository);
        $command = new UpdateTaskStatusCommand($uuid, TaskStatus::COMPLETED);

        $handler->handle($command);

        $this->assertSame(TaskStatus::COMPLETED, $task->getStatus());
    }

    private function createTask(Uuid $id, TaskStatus $status): Task
    {
        $task = Task::fromDatabase(
            Title::fromString('Test'),
            WebsiteUrl::fromString('https://example.com'),
            Description::fromString('Desc'),
            Phone::fromString('+48123456789'),
            Email::fromString('a@b.com'),
            Address::fromString('ul. Test 1'),
            $status
        );
        $task->setId($id);

        return $task;
    }
}
