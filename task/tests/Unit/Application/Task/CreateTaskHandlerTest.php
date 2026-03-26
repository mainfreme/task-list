<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Task;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\Phone;
use App\Shared\Domain\ValueObject\Uuid;
use App\Task\Application\Command\CreateTask\CreateTaskCommand;
use App\Task\Application\Command\CreateTask\CreateTaskHandler;
use App\Task\Application\DTO\TaskDTO;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Repository\TaskRepositoryInterface;
use App\Task\Domain\ValueObject\ApplicationManagerId;
use App\Task\Domain\ValueObject\DeliveryAddress;
use App\Task\Domain\ValueObject\Description;
use App\Task\Domain\ValueObject\DueDate;
use App\Task\Domain\ValueObject\Email;
use App\Task\Domain\ValueObject\Title;
use App\Task\Domain\ValueObject\WebsiteUrl;
use Mockery;
use PHPUnit\Framework\TestCase;

final class CreateTaskHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_task_with_required_fields_and_saves(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Task $task) use ($uuid) {
                $task->setId($uuid);

                return $task->getStatus()->value === 'pending';
            }));

        $handler = new CreateTaskHandler($repository);
        $command = new CreateTaskCommand(
            Title::fromString('Zadanie testowe'),
            WebsiteUrl::fromString('https://example.com'),
            Description::fromString('Opis zadania'),
            Phone::fromString('+48123456789'),
            Email::fromString('test@example.com'),
            Address::fromString('ul. Testowa 1')
        );

        $result = $handler->handle($command);

        $this->assertInstanceOf(TaskDTO::class, $result);
        $this->assertSame('Zadanie testowe', $result->title->getValue());
        $this->assertSame('https://example.com', $result->websiteUrl->getValue());
        $this->assertSame('test@example.com', $result->email->getValue());
        $this->assertSame('pending', $result->status->value);
        $this->assertNull($result->applicationManagerId);
        $this->assertNull($result->dueDate);
        $this->assertNull($result->deliveryAddress);
    }

    public function test_handle_creates_task_with_all_optional_fields(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $appManagerId = ApplicationManagerId::fromString('550e8400-e29b-41d4-a716-446655440001');

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Task $task) use ($uuid) {
                $task->setId($uuid);

                return true;
            }));

        $handler = new CreateTaskHandler($repository);
        $command = new CreateTaskCommand(
            Title::fromString('Zadanie pełne'),
            WebsiteUrl::fromString('https://site.com'),
            Description::fromString('Opis'),
            Phone::fromString('+48987654321'),
            Email::fromString('full@example.com'),
            Address::fromString('ul. Pełna 10'),
            $appManagerId,
            DueDate::fromString('2025-12-31'),
            DeliveryAddress::fromString('ul. Dostawy 5')
        );

        $result = $handler->handle($command);

        $this->assertSame($appManagerId->getValue(), $result->applicationManagerId?->getValue());
        $this->assertNotNull($result->dueDate);
        $this->assertSame('ul. Dostawy 5', $result->deliveryAddress?->getValue());
    }
}
