<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Auth;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Enum\UserRoleEnum;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Infrastructure\Middleware\UserJwtMiddleware;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class UserJwtMiddlewareTest extends TestCase
{
    private const JWT_SECRET = 'test-secret-key-for-testing-purposes';
    private const JWT_ALGORITHM = 'HS256';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_401_when_no_authorization_header(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->remove('Authorization');

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Unauthorized', $data['error']);
        $this->assertStringContainsString('JWT token is required', $data['message']);
    }

    public function test_returns_401_when_authorization_header_empty(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', '');

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_returns_401_when_token_is_invalid(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer invalid-token');

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Unauthorized', $data['error']);
        $this->assertStringContainsString('Invalid token', $data['message']);
    }

    public function test_returns_401_when_token_missing_user_id_claim(): void
    {
        $token = JWT::encode(['some' => 'data'], self::JWT_SECRET, self::JWT_ALGORITHM);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('user_id', $data['message']);
    }

    public function test_returns_401_when_user_not_found_in_repository(): void
    {
        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['user_id' => $userId], self::JWT_SECRET, self::JWT_ALGORITHM);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn (Uuid $id) => $id->getValue() === $userId))
            ->andReturn(null);

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Unauthorized', $data['error']);
        $this->assertStringContainsString('User not found', $data['message']);
    }

    public function test_passes_request_when_token_valid_and_user_exists(): void
    {
        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['user_id' => $userId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $user = $this->createUser($userId);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn (Uuid $id) => $id->getValue() === $userId))
            ->andReturn($user);

        $middleware = new UserJwtMiddleware($repository);
        $nextCalled = false;
        $next = function () use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['ok' => true], 200);
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled, 'Next middleware must be called on success');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_sets_user_id_and_user_on_request_attributes_when_authenticated(): void
    {
        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['user_id' => $userId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $user = $this->createUser($userId);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($user);

        $middleware = new UserJwtMiddleware($repository);
        $nextCalled = false;
        $capturedUserId = null;
        $capturedUser = null;
        $next = function (Request $req) use (&$nextCalled, &$capturedUserId, &$capturedUser) {
            $nextCalled = true;
            $capturedUserId = $req->attributes->get('user_id');
            $capturedUser = $req->attributes->get('user');
            return response()->json(['ok' => true], 200);
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled, 'Next middleware must be called');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotNull($capturedUserId, 'user_id must be set on request');
        $this->assertSame($userId, $capturedUserId->getValue());
        $this->assertSame($user, $capturedUser, 'user must be set on request');
    }

    public function test_strips_bearer_prefix_from_authorization_header(): void
    {
        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['user_id' => $userId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $user = $this->createUser($userId);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($user);

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_accepts_token_without_bearer_prefix(): void
    {
        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['user_id' => $userId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $user = $this->createUser($userId);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', $token);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($user);

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_returns_401_when_token_expired(): void
    {
        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode([
            'user_id' => $userId,
            'exp' => time() - 3600, // expired 1 hour ago
        ], self::JWT_SECRET, self::JWT_ALGORITHM);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new UserJwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('Invalid token', $data['message']);
    }

    private function createUser(string $userId): User
    {
        return User::fromDatabase(
            Uuid::fromString($userId),
            'Test User',
            Email::fromString('test@example.com'),
            password_hash('password', PASSWORD_BCRYPT),
            UserRoleEnum::USER
        );
    }
}
