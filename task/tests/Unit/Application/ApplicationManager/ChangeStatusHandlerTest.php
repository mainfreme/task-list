<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ApplicationManager;

use App\ApplicationManager\Application\Command\UpdateApplicationManager\ChangeStatusCommand;
use App\ApplicationManager\Application\Command\UpdateApplicationManager\ChangeStatusHandler;
use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ChangeStatusHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_activates_application_manager_when_is_active_true(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $applicationManager = $this->createApplicationManager(isActive: false);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($arg) => $arg->getValue() === $uuid->getValue()))
            ->andReturn($applicationManager);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (ApplicationManager $am) => $am->isActive() === true));

        $handler = new ChangeStatusHandler($repository);
        $command = new ChangeStatusCommand($uuid, true);

        $handler->handle($command);

        $this->assertTrue($applicationManager->isActive());
    }

    public function test_handle_deactivates_application_manager_when_is_active_false(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $applicationManager = $this->createApplicationManager(isActive: true);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andReturn($applicationManager);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (ApplicationManager $am) => $am->isActive() === false));

        $handler = new ChangeStatusHandler($repository);
        $command = new ChangeStatusCommand($uuid, false);

        $handler->handle($command);

        $this->assertFalse($applicationManager->isActive());
    }

    public function test_handle_activates_already_active_application_manager_still_saves(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $applicationManager = $this->createApplicationManager(isActive: true);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($applicationManager);
        $repository->shouldReceive('save')->once();

        $handler = new ChangeStatusHandler($repository);
        $handler->handle(new ChangeStatusCommand($uuid, true));

        $this->assertTrue($applicationManager->isActive());
    }

    public function test_handle_throws_when_application_manager_not_found(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(ApplicationManagerNotFoundException::byId($uuid->getValue()));
        $repository->shouldNotReceive('save');

        $handler = new ChangeStatusHandler($repository);
        $command = new ChangeStatusCommand($uuid, true);

        $this->expectException(ApplicationManagerNotFoundException::class);
        $this->expectExceptionMessage('ApplicationManager with ID');

        $handler->handle($command);
    }

    private function createApplicationManager(bool $isActive): ApplicationManager
    {
        return ApplicationManager::create(
            ApplicationName::fromString('Test App'),
            ApiKey::fromString(str_repeat('a', 32)),
            null,
            $isActive
        );
    }
}
