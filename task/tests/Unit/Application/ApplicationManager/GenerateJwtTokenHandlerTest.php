<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ApplicationManager;

use App\ApplicationManager\Application\Command\GenerateJwtToken\GenerateJwtTokenCommand;
use App\ApplicationManager\Application\Command\GenerateJwtToken\GenerateJwtTokenHandler;
use App\ApplicationManager\Application\DTO\GeneratedApplicationJwtTokenDTO;
use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\InactiveApplicationManagerForTokenException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\Service\ApplicationJwtTokenGeneratorInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class GenerateJwtTokenHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_returns_dto_and_uses_explicit_expiration_when_given(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $app = $this->createActiveApplication($uuid);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn ($id) => $id->getValue() === $uuid->getValue()))
            ->andReturn($app);

        $jwt = Mockery::mock(ApplicationJwtTokenGeneratorInterface::class);
        $jwt->shouldReceive('defaultExpirationMinutes')->never();
        $jwt->shouldReceive('generate')
            ->once()
            ->with($uuid->getValue(), 'Test App', 15)
            ->andReturn('signed-token');

        $handler = new GenerateJwtTokenHandler($repository, $jwt);
        $result = $handler->handle(new GenerateJwtTokenCommand($uuid, 15));

        $this->assertInstanceOf(GeneratedApplicationJwtTokenDTO::class, $result);
        $this->assertSame('signed-token', $result->token);
        $this->assertSame(15, $result->expiresInMinutes);
        $this->assertSame('Bearer', $result->tokenType);
    }

    public function test_handle_uses_default_expiration_when_command_has_none(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $app = $this->createActiveApplication($uuid);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $jwt = Mockery::mock(ApplicationJwtTokenGeneratorInterface::class);
        $jwt->shouldReceive('defaultExpirationMinutes')->once()->andReturn(1440);
        $jwt->shouldReceive('generate')
            ->once()
            ->with($uuid->getValue(), 'Test App', 1440)
            ->andReturn('t');

        $handler = new GenerateJwtTokenHandler($repository, $jwt);
        $result = $handler->handle(new GenerateJwtTokenCommand($uuid, null));

        $this->assertSame(1440, $result->expiresInMinutes);
    }

    public function test_handle_throws_when_application_inactive(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $app = $this->createActiveApplication($uuid);
        $app->deactivate();

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $jwt = Mockery::mock(ApplicationJwtTokenGeneratorInterface::class);
        $jwt->shouldNotReceive('generate');

        $handler = new GenerateJwtTokenHandler($repository, $jwt);

        $this->expectException(InactiveApplicationManagerForTokenException::class);
        $handler->handle(new GenerateJwtTokenCommand($uuid, 60));
    }

    private function createActiveApplication(Uuid $id): ApplicationManager
    {
        $entity = ApplicationManager::create(
            ApplicationName::fromString('Test App'),
            ApiKey::fromString(str_repeat('a', 32)),
            null,
            true,
            null
        );
        $entity->setId($id);

        return $entity;
    }
}
