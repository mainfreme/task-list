<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Middleware;

use Closure;
use App\ApplicationManager\Domain\Exception\ApplicationManagerNotFoundException;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApiKey;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiKeyMiddleware
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
        $apiKey = $request->header('X-API-Key') ?? $request->header('Authorization');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API Key is required',
                'message' => 'Please provide API Key in X-API-Key header or Authorization header',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Remove "Bearer " prefix if present
        $apiKey = str_replace('Bearer ', '', $apiKey);

        try {
            $apiKeyValueObject = ApiKey::fromString($apiKey);
            $applicationManager = $this->repository->findByApiKey($apiKeyValueObject);

            // Check if application is active
            if (!$applicationManager->isActive()) {
                return response()->json([
                    'error' => 'Application is inactive',
                    'message' => 'The application associated with this API Key is currently inactive',
                ], Response::HTTP_FORBIDDEN);
            }

            // Check IP whitelist if configured
            $ipWhitelist = $applicationManager->getIpWhitelist();
            if ($ipWhitelist !== null && count($ipWhitelist) > 0) {
                $clientIp = $request->ip();
                if (!in_array($clientIp, $ipWhitelist, true)) {
                    return response()->json([
                        'error' => 'IP address not allowed',
                        'message' => 'Your IP address is not in the whitelist for this application',
                    ], Response::HTTP_FORBIDDEN);
                }
            }

            // Attach application manager ID to request for use in controllers
            $request->attributes->set('application_manager_id', $applicationManager->getId());

        } catch (ApplicationManagerNotFoundException $e) {
            return response()->json([
                'error' => 'Invalid API Key',
                'message' => 'The provided API Key is invalid or not found',
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Invalid API Key format',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
