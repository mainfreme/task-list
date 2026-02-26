<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ApplicationManager;

use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Application\Query\GetApplicationManager\GetApplicationManagerHandler;
use App\ApplicationManager\Application\Query\GetApplicationManager\GetApplicationManagerQuery;
use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class GetApplicationManagerHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_when_application_manager_not_found(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andThrow(ApplicationManagerNotFoundException::byId($uuid->getValue()));

        $handler = new GetApplicationManagerHandler($repository);
        $query = new GetApplicationManagerQuery($uuid);

        $this->expectException(ApplicationManagerNotFoundException::class);
        $this->expectExceptionMessage('ApplicationManager with ID');

        $handler->handle($query);
    }

    public function test_handle_returns_dto_with_application_manager_data(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $app = $this->createApplicationManager($uuid, isActive: true);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $handler = new GetApplicationManagerHandler($repository);
        $query = new GetApplicationManagerQuery($uuid);

        $result = $handler->handle($query);

        $this->assertInstanceOf(ApplicationManagerDTO::class, $result);
        $this->assertSame($uuid->getValue(), $result->id->getValue());
        $this->assertSame('Test App', $result->name->getValue());
        $this->assertTrue($result->isActive);
    }

    public function test_handle_returns_inactive_status_when_application_deactivated(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $app = $this->createApplicationManager($uuid, isActive: false);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $handler = new GetApplicationManagerHandler($repository);
        $query = new GetApplicationManagerQuery($uuid);

        $result = $handler->handle($query);

        $this->assertFalse($result->isActive);
    }

    public function test_handle_includes_ip_whitelist_when_configured(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $whitelist = IpWhitelist::fromArray(['192.168.1.1']);
        $app = $this->createApplicationManager($uuid, isActive: true, ipWhitelist: $whitelist);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $handler = new GetApplicationManagerHandler($repository);
        $query = new GetApplicationManagerQuery($uuid);

        $result = $handler->handle($query);

        $this->assertNotNull($result->ipWhitelist);
        $this->assertSame(['192.168.1.1'], $result->ipWhitelist->toArray());
    }

    private function createApplicationManager(Uuid $id, bool $isActive, ?IpWhitelist $ipWhitelist = null): ApplicationManager
    {
        $app = ApplicationManager::create(
            ApplicationName::fromString('Test App'),
            ApiKey::fromString(str_repeat('a', 32)),
            null,
            $isActive,
            $ipWhitelist
        );
        $app->setId($id);
        return $app;
    }
}
