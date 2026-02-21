<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Task List API',
    description: 'API documentation for the Task List application',
    contact: new OA\Contact(email: 'support@example.com')
)]
#[OA\Server(
    url: '/api',
    description: 'Main API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'jwt',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class OpenApi
{
}
