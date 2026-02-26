<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Task;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Task\Application\DTO\TaskDTO;
use App\Task\Application\Query\GetTask\GetTaskHandler;
use App\Task\Application\Query\GetTask\GetTaskQuery;
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

final class GetTaskHandlerTest extends TestCase
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

        $handler = new GetTaskHandler($repository);
        $query = new GetTaskQuery($uuid);

        $this->expectException(TaskNotFoundException::class);
        $this->expectExceptionMessage('Task with ID');

        $handler->handle($query);
    }

    public function test_handle_returns_dto_with_task_data(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $task = $this->createTask($uuid);

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andReturn($task);

        $handler = new GetTaskHandler($repository);
        $query = new GetTaskQuery($uuid);

        $result = $handler->handle($query);

        $this->assertInstanceOf(TaskDTO::class, $result);
        $this->assertSame($uuid->getValue(), $result->id->getValue());
        $this->assertSame('Zadanie do pobrania', $result->title->getValue());
        $this->assertSame('completed', $result->status->value);
    }

    private function createTask(Uuid $id): Task
    {
        $task = Task::fromDatabase(
            Title::fromString('Zadanie do pobrania'),
            WebsiteUrl::fromString('https://example.com'),
            Description::fromString('Opis'),
            Phone::fromString('+48123456789'),
            Email::fromString('get@example.com'),
            Address::fromString('ul. Test 1'),
            TaskStatus::COMPLETED
        );
        $task->setId($id);
        return $task;
    }
}
