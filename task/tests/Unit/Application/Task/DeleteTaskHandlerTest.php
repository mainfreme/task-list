<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Task;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Task\Application\Command\DeleteTask\DeleteTaskCommand;
use App\Task\Application\Command\DeleteTask\DeleteTaskHandler;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Exception\TaskNotFoundException;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class DeleteTaskHandlerTest extends TestCase
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
        $repository->shouldNotReceive('softDelete');

        $handler = new DeleteTaskHandler($repository);
        $command = new DeleteTaskCommand($uuid);

        $this->expectException(TaskNotFoundException::class);
        $this->expectExceptionMessage('Task with ID');

        $handler->handle($command);
    }

    public function test_handle_calls_soft_delete_when_task_found(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $task = $this->createTask($uuid);
        $capturedTask = null;

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->getValue() === $uuid->getValue()))
            ->andReturn($task);
        $repository->shouldReceive('softDelete')
            ->once()
            ->andReturnUsing(function (Task $t) use (&$capturedTask) {
                $capturedTask = $t;
            });

        $handler = new DeleteTaskHandler($repository);
        $command = new DeleteTaskCommand($uuid);

        $handler->handle($command);

        $this->assertSame($task, $capturedTask);
        $this->assertSame($uuid->getValue(), $capturedTask->getId()?->getValue());
    }

    private function createTask(Uuid $id): Task
    {
        $task = Task::fromDatabase(
            Title::fromString('Test'),
            WebsiteUrl::fromString('https://example.com'),
            Description::fromString('Desc'),
            Phone::fromString('+48123456789'),
            Email::fromString('a@b.com'),
            Address::fromString('ul. Test 1'),
            TaskStatus::PENDING
        );
        $task->setId($id);
        return $task;
    }
}
