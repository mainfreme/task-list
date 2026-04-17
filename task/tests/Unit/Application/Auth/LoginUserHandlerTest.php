<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Auth;

use App\Auth\Application\Command\LoginUser\LoginUserCommand;
use App\Auth\Application\Command\LoginUser\LoginUserHandler;
use App\Auth\Application\DTO\AuthResponseDTO;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Enum\UserRoleEnum;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Service\JwtTokenServiceInterface;
use App\Auth\Domain\ValueObject\UserIdentity;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class LoginUserHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_when_user_not_found(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findByEmail')
            ->once()
            ->andReturn(null);

        $jwtService = Mockery::mock(JwtTokenServiceInterface::class);
        $jwtService->shouldNotReceive('generateToken');

        $handler = new LoginUserHandler($repository, $jwtService);
        $command = new LoginUserCommand(
            Email::fromString('nonexistent@example.com'),
            'any-password'
        );

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('Invalid email or password');

        $handler->handle($command);
    }

    public function test_handle_throws_when_password_incorrect(): void
    {
        $user = $this->createUserWithPassword('correct-password');

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findByEmail')
            ->once()
            ->andReturn($user);

        $jwtService = Mockery::mock(JwtTokenServiceInterface::class);
        $jwtService->shouldNotReceive('generateToken');

        $handler = new LoginUserHandler($repository, $jwtService);
        $command = new LoginUserCommand(
            Email::fromString('user@example.com'),
            'wrong-password'
        );

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('Invalid email or password');

        $handler->handle($command);
    }

    public function test_handle_returns_auth_response_when_credentials_valid(): void
    {
        $user = $this->createUserWithPassword('valid-password');

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findByEmail')
            ->once()
            ->andReturn($user);

        $jwtService = Mockery::mock(JwtTokenServiceInterface::class);
        $jwtService->shouldReceive('generateToken')
            ->once()
            ->with(Mockery::type(UserIdentity::class))
            ->andReturn('jwt-token-123');
        $jwtService->shouldReceive('getExpirationMinutes')
            ->once()
            ->andReturn(1440);

        $handler = new LoginUserHandler($repository, $jwtService);
        $command = new LoginUserCommand(
            Email::fromString('user@example.com'),
            'valid-password'
        );

        $result = $handler->handle($command);

        $this->assertInstanceOf(AuthResponseDTO::class, $result);
        $this->assertSame('jwt-token-123', $result->token);
        $this->assertSame(1440, $result->expiresIn);
        $this->assertSame('user@example.com', $result->user->email->getValue());
    }

    public function test_handle_throws_on_empty_password_with_existing_user(): void
    {
        $user = $this->createUserWithPassword('secret');

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findByEmail')->once()->andReturn($user);

        $jwtService = Mockery::mock(JwtTokenServiceInterface::class);
        $jwtService->shouldNotReceive('generateToken');

        $handler = new LoginUserHandler($repository, $jwtService);
        $command = new LoginUserCommand(Email::fromString('user@example.com'), '');

        $this->expectException(InvalidCredentialsException::class);

        $handler->handle($command);
    }

    private function createUserWithPassword(string $password): User
    {
        return User::fromDatabase(
            Uuid::fromString('550e8400-e29b-41d4-a716-446655440000'),
            'Test User',
            Email::fromString('user@example.com'),
            password_hash($password, PASSWORD_BCRYPT),
            UserRoleEnum::USER
        );
    }
}
