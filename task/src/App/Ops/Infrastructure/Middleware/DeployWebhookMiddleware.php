<?php

declare(strict_types=1);

namespace App\Ops\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeployWebhookMiddleware
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configured = config('deploy.webhook_secret');
        if ($configured === null || $configured === '') {
            return response()->json([
                'success' => false,
                'message' => 'Deploy webhook is not configured',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $header = $request->header('Authorization', '');
        $token = str_starts_with($header, 'Bearer ')
            ? substr($header, 7)
            : $request->header('X-Deploy-Token', '');

        if ($token === '' || ! hash_equals((string) $configured, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid deploy webhook credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
