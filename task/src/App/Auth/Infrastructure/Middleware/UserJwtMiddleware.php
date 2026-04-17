<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Middleware;

use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserJwtMiddleware
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
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
                'error' => 'Unauthorized',
                'message' => 'JWT token is required. Please provide token in Authorization header as: Bearer {token}',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = $this->verifyToken($token);
            $userId = Uuid::fromString($this->extractUserId($decoded));
            $user = $this->repository->findById($userId);

            if (!$user) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'User not found',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Attach user to request for use in controllers
            $request->attributes->set('user_id', $user->getId());
            $request->attributes->set('user', $user);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid token: ' . $e->getMessage(),
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

    private function extractUserId(object $decoded): string
    {
        if (!isset($decoded->user_id)) {
            throw new \RuntimeException('Token does not contain user_id claim');
        }

        return (string) $decoded->user_id;
    }

    private function getJwtSecret(): string
    {
        $secret = config('auth_jwt.secret');

        if (!is_string($secret) || $secret === '') {
            throw new \RuntimeException('JWT secret is not configured (auth_jwt.secret / JWT_SECRET).');
        }

        return $secret;
    }

    private function getJwtAlgorithm(): string
    {
        return (string) config('auth_jwt.algorithm', 'HS256');
    }
}
