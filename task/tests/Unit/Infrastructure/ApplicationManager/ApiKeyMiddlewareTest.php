<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\ApplicationManager;

use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Infrastructure\Middleware\ApiKeyMiddleware;
use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class ApiKeyMiddlewareTest extends TestCase
{
    private const VALID_API_KEY = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_401_when_no_api_key_header(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->remove('X-API-Key');
        $request->headers->remove('Authorization');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findByApiKey');

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('API Key is required', $data['error']);
        $this->assertStringContainsString('X-API-Key', $data['message']);
    }

    public function test_returns_401_when_authorization_header_empty(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', '');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findByApiKey');

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_strips_bearer_prefix_from_authorization_header(): void
    {
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')
            ->once()
            ->with(Mockery::on(fn (ApiKey $key) => $key->value() === self::VALID_API_KEY))
            ->andReturn($app);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $nextCalled = false;
        $next = function () use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['ok' => true], 200);
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_prefers_x_api_key_over_authorization(): void
    {
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')
            ->once()
            ->with(Mockery::on(fn (ApiKey $key) => $key->value() === self::VALID_API_KEY))
            ->andReturn($app);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('X-API-Key', self::VALID_API_KEY);
        $request->headers->set('Authorization', 'Bearer other-key');

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_returns_403_when_application_inactive(): void
    {
        $app = $this->createApplicationManager(active: false, ipWhitelist: null);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')->once()->andReturn($app);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('X-API-Key', self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Application is inactive', $data['error']);
    }

    public function test_returns_403_when_ip_not_in_whitelist(): void
    {
        $whitelist = IpWhitelist::fromArray(['192.168.1.1']);
        $app = $this->createApplicationManager(active: true, ipWhitelist: $whitelist);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')->once()->andReturn($app);

        $request = Request::create('/api/test', 'GET', [], [], [], [
            'REMOTE_ADDR' => '10.0.0.1',
        ]);
        $request->headers->set('X-API-Key', self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('IP address not allowed', $data['error']);
    }

    public function test_passes_when_ip_in_whitelist(): void
    {
        $whitelist = IpWhitelist::fromArray(['192.168.1.100']);
        $app = $this->createApplicationManager(active: true, ipWhitelist: $whitelist);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')->once()->andReturn($app);

        $request = Request::create('/api/test', 'GET', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.100',
        ]);
        $request->headers->set('X-API-Key', self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $nextCalled = false;
        $next = function () use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['ok' => true], 200);
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_passes_when_whitelist_empty_but_not_null(): void
    {
        $whitelist = IpWhitelist::fromArray([]);
        $this->assertTrue($whitelist->isEmpty());
        $app = $this->createApplicationManager(active: true, ipWhitelist: $whitelist);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')->once()->andReturn($app);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('X-API-Key', self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_returns_401_when_api_key_not_found(): void
    {
        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')
            ->once()
            ->andThrow(ApplicationManagerNotFoundException::byApiKey(self::VALID_API_KEY));

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('X-API-Key', self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Invalid API Key', $data['error']);
    }

    public function test_returns_400_when_api_key_format_invalid(): void
    {
        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldNotReceive('findByApiKey');

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('X-API-Key', 'too-short');

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Invalid API Key format', $data['error']);
        $this->assertStringContainsString('32 characters', $data['message']);
    }

    public function test_sets_application_manager_id_on_request_when_successful(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);
        $app->setId($uuid);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')->once()->andReturn($app);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('X-API-Key', self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $next = function (Request $req) use ($uuid) {
            $this->assertSame($uuid->getValue(), $req->attributes->get('application_manager_id'));
            return response()->json(['ok' => true], 200);
        };

        $middleware->handle($request, $next);
    }

    public function test_passes_when_ip_whitelist_null(): void
    {
        $app = $this->createApplicationManager(active: true, ipWhitelist: null);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('findByApiKey')->once()->andReturn($app);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('X-API-Key', self::VALID_API_KEY);

        $middleware = new ApiKeyMiddleware($repository);
        $next = fn () => response()->json(['ok' => true], 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(200, $response->getStatusCode());
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
