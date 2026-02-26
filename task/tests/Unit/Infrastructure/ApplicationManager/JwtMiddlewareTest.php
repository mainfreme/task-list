<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\ApplicationManager;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Infrastructure\Middleware\JwtMiddleware;
use App\Shared\Domain\ValueObject\Uuid;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class JwtMiddlewareTest extends TestCase
{
    private const JWT_SECRET = 'test-secret-key-for-testing-purposes';
    private const JWT_ALGORITHM = 'HS256';
    private const VALID_API_KEY = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_401_when_no_authorization_header(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->remove('Authorization');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('JWT token is required', $data['error']);
        $this->assertStringContainsString('Authorization header', $data['message']);
    }

    public function test_returns_401_when_authorization_header_empty(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', '');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_returns_401_when_token_is_invalid(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer invalid-token');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Invalid JWT token', $data['error']);
    }

    public function test_returns_401_when_token_missing_application_id_claim(): void
    {
        $token = JWT::encode(['some' => 'data'], self::JWT_SECRET, self::JWT_ALGORITHM);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('application_id', $data['message']);
    }

    public function test_returns_401_when_application_manager_not_found(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn (Uuid $id) => $id->getValue() === $appId))
            ->andThrow(ApplicationManagerNotFoundException::byId($appId));

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Invalid JWT token', $data['error']);
        $this->assertStringContainsString('not found', $data['message']);
    }

    public function test_returns_403_when_application_is_inactive(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $app = $this->createApplicationManager(active: false, ipWhitelist: null);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Application is inactive', $data['error']);
    }

    public function test_returns_403_when_ip_not_in_whitelist(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $whitelist = IpWhitelist::fromArray(['192.168.1.1']);
        $app = $this->createApplicationManager(active: true, ipWhitelist: $whitelist);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET', [], [], [], [
            'REMOTE_ADDR' => '10.0.0.1',
        ]);
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('IP address not allowed', $data['error']);
    }

    public function test_passes_when_ip_in_whitelist(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $whitelist = IpWhitelist::fromArray(['192.168.1.100']);
        $app = $this->createApplicationManager(active: true, ipWhitelist: $whitelist);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.100',
        ]);
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $nextCalled = false;
        $next = function () use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['ok' => true], 200);
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled, 'Next middleware must be called when IP is allowed');
        $this->assertSame(200, $response->getStatusCode());
    }

    /** Przypadek brzegowy: whitelist null – middleware przepuszcza request */
    public function test_passes_when_ip_whitelist_is_null(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    /** Przypadek brzegowy: whitelist pusta (nie null) – middleware przepuszcza request */
    public function test_passes_when_ip_whitelist_is_empty(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $whitelist = IpWhitelist::fromArray([]);
        $app = $this->createApplicationManager(active: true, ipWhitelist: $whitelist);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_sets_application_manager_id_and_entity_on_request_attributes(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $nextCalled = false;
        $capturedAppId = null;
        $capturedApp = null;
        $next = function (Request $req) use (&$nextCalled, &$capturedAppId, &$capturedApp) {
            $nextCalled = true;
            $capturedAppId = $req->attributes->get('application_manager_id');
            $capturedApp = $req->attributes->get('application_manager');
            return response()->json(['ok' => true], 200);
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled, 'Next middleware must be called');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($appId, $capturedAppId, 'application_manager_id must be set');
        $this->assertSame($app, $capturedApp, 'application_manager must be set');
    }

    public function test_strips_bearer_prefix_from_authorization_header(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_accepts_token_without_bearer_prefix(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode(['application_id' => $appId], self::JWT_SECRET, self::JWT_ALGORITHM);
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);
        $app->setId(Uuid::fromString($appId));

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($app);

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_returns_401_when_token_expired(): void
    {
        $appId = '550e8400-e29b-41d4-a716-446655440000';
        $token = JWT::encode([
            'application_id' => $appId,
            'exp' => time() - 3600, // expired 1 hour ago
        ], self::JWT_SECRET, self::JWT_ALGORITHM);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findById');

        $middleware = new JwtMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    private function createApplicationManager(bool $active, ?IpWhitelist $ipWhitelist): ApplicationManager
    {
        return ApplicationManager::create(
            ApplicationName::fromString('Test App'),
            ApiKey::fromString(self::VALID_API_KEY),
            null,
            $active,
            $ipWhitelist
        );
    }
}
