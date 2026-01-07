<?php

declare(strict_types=1);

namespace App\UI\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

abstract class ApiController extends Controller
{
    /**
     * Zwraca odpowiedź sukcesu (200 OK)
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function success($data = null, ?string $message = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    /**
     * Zwraca odpowiedź utworzenia zasobu (201 Created)
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function created($data = null, ?string $message = 'Resource created successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    /**
     * Zwraca odpowiedź bez zawartości (204 No Content)
     *
     * @return JsonResponse
     */
    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Zwraca odpowiedź błędu walidacji (400 Bad Request)
     *
     * @param string|null $message
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function badRequest(?string $message = 'Bad request', $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 400);
    }

    /**
     * Zwraca odpowiedź nieautoryzowanego dostępu (401 Unauthorized)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function unauthorized(?string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }

    /**
     * Zwraca odpowiedź zabronionego dostępu (403 Forbidden)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function forbidden(?string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    /**
     * Zwraca odpowiedź nie znaleziono zasobu (404 Not Found)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function notFound(?string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    /**
     * Zwraca odpowiedź błędu walidacji (422 Unprocessable Entity)
     *
     * @param string|null $message
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function unprocessableEntity(?string $message = 'Validation failed', $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Zwraca odpowiedź błędu serwera (500 Internal Server Error)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function serverError(?string $message = 'Internal server error'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }

    /**
     * Zwraca odpowiedź niemożliwe do przetworzenia (503 Service Unavailable)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function serviceUnavailable(?string $message = 'Service unavailable'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 503);
    }

    /**
     * Zwraca odpowiedź konfliktu (409 Conflict)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function conflict(?string $message = 'Conflict'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 409);
    }

    /**
     * Zwraca odpowiedź zaakceptowaną (202 Accepted)
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function accepted($data = null, ?string $message = 'Request accepted'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 202);
    }

    /**
     * Zwraca odpowiedź błędu ogólnego (500 Internal Server Error)
     *
     * @param string|null $message
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function error(?string $message = 'An error occurred', $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 500);
    }

    /**
     * Zwraca niestandardową odpowiedź JSON
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function jsonResponse($data, int $statusCode = 200, array $headers = []): JsonResponse
    {
        return response()->json($data, $statusCode, $headers);
    }
}

