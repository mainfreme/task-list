<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\TaskStatus;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use PHPUnit\Framework\TestCase;

final class TaskEntityTest extends TestCase
{
    public function test_create_sets_pending_status(): void
    {
        $task = Task::create(
            Title::fromString('Test'),
            WebsiteUrl::fromString('https://example.com'),
            Description::fromString('Desc'),
            Phone::fromString('+48123456789'),
            Email::fromString('a@b.com'),
            Address::fromString('ul. Test 1')
        );

        $this->assertSame(TaskStatus::PENDING, $task->getStatus());
    }

    public function test_set_status_changes_status(): void
    {
        $task = $this->createTask();

        $task->setStatus(TaskStatus::IN_PROGRESS);
        $this->assertSame(TaskStatus::IN_PROGRESS, $task->getStatus());

        $task->setStatus(TaskStatus::COMPLETED);
        $this->assertSame(TaskStatus::COMPLETED, $task->getStatus());

        $task->setStatus(TaskStatus::CANCELLED);
        $this->assertSame(TaskStatus::CANCELLED, $task->getStatus());
    }

    public function test_from_database_preserves_given_status(): void
    {
        $task = Task::fromDatabase(
            Title::fromString('Test'),
            WebsiteUrl::fromString('https://example.com'),
            Description::fromString('Desc'),
            Phone::fromString('+48123456789'),
            Email::fromString('a@b.com'),
            Address::fromString('ul. Test 1'),
            TaskStatus::COMPLETED
        );

        $this->assertSame(TaskStatus::COMPLETED, $task->getStatus());
    }

    private function createTask(): Task
    {
        return Task::create(
            Title::fromString('Test'),
            WebsiteUrl::fromString('https://example.com'),
            Description::fromString('Desc'),
            Phone::fromString('+48123456789'),
            Email::fromString('a@b.com'),
            Address::fromString('ul. Test 1')
        );
    }
}
