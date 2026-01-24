<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Middleware;

use Closure;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\Uuid;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class JwtMiddleware
{
    public function __construct(
        private readonly ApplicationManagerRepositoryInterface $repository
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json([
                'error' => 'JWT token is required',
                'message' => 'Please provide JWT token in Authorization header as: Bearer {token}',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = $this->verifyToken($token);
            $uuid = $this->extractUuid($decoded);
            $applicationManager = $this->repository->findById($uuid);

            // Check if application is active
            if (!$applicationManager->isActive()) {
                return response()->json([
                    'error' => 'Application is inactive',
                    'message' => 'The application associated with this JWT token is currently inactive',
                ], Response::HTTP_FORBIDDEN);
            }

            // Check IP whitelist if configured
            $ipWhitelist = $applicationManager->getIpWhitelist();
            if ($ipWhitelist !== null && !$ipWhitelist->isEmpty()) {
                $clientIp = $request->ip();
                if (!$ipWhitelist->allows($clientIp)) {
                    return response()->json([
                        'error' => 'IP address not allowed',
                        'message' => 'Your IP address is not in the whitelist for this application',
                    ], Response::HTTP_FORBIDDEN);
                }
            }

            // Attach application manager ID to request for use in controllers
            $request->attributes->set('application_manager_id', $applicationManager->getId()?->getValue());
            $request->attributes->set('application_manager', $applicationManager);

        } catch (ApplicationManagerNotFoundException $e) {
            return response()->json([
                'error' => 'Invalid JWT token',
                'message' => 'The application associated with this JWT token was not found',
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid JWT token',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $authorization = $request->header('Authorization');

        if (!$authorization) {
            return null;
        }

        // Remove "Bearer " prefix
        if (str_starts_with($authorization, 'Bearer ')) {
            return substr($authorization, 7);
        }

        return $authorization;
    }

    private function verifyToken(string $token): object
    {
        $secret = $this->getJwtSecret();
        $algorithm = $this->getJwtAlgorithm();

        try {
            return JWT::decode($token, new Key($secret, $algorithm));
        } catch (\Exception $e) {
            throw new \RuntimeException('Token verification failed: ' . $e->getMessage());
        }
    }

    private function extractUuid(object $decoded): Uuid
    {
        if (!isset($decoded->application_id)) {
            throw new \RuntimeException('Token does not contain application_id claim');
        }

        return Uuid::fromString((string) $decoded->application_id);
    }

    private function getJwtSecret(): string
    {
        $secret = env('JWT_SECRET');
        
        if (!$secret) {
            throw new \RuntimeException('JWT_SECRET is not configured in .env file');
        }

        return $secret;
    }

    private function getJwtAlgorithm(): string
    {
        return env('JWT_ALGORITHM', 'HS256');
    }
}
