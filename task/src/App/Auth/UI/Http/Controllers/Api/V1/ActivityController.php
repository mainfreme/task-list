<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controllers\Api\V1;

use App\Auth\Application\Command\LogActivity\LogActivityCommand;
use App\Auth\Application\Command\LogActivity\LogActivityHandler;
use App\Auth\UI\Http\Requests\V1\LogActivityRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Activity', description: 'User activity logging endpoints')]
final class ActivityController extends ApiController
{
    public function __construct(
        private readonly LogActivityHandler $logActivityHandler,
    ) {
    }

    #[OA\Post(
        path: '/v1/auth/activity',
        summary: 'Log user activity',
        description: 'Asynchronously logs user activity (login, page views, etc.) to RabbitMQ queue for processing',
        tags: ['Activity'],
        security: [['user_jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['url', 'ip_address', 'user_agent', 'action'],
                properties: [
                    new OA\Property(
                        property: 'url',
                        type: 'string',
                        example: 'https://app.softwellhouse.pl/dashboard'
                    ),
                    new OA\Property(
                        property: 'ip_address',
                        type: 'string',
                        format: 'ipv4',
                        example: '192.168.1.100'
                    ),
                    new OA\Property(
                        property: 'user_agent',
                        type: 'string',
                        example: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'
                    ),
                    new OA\Property(
                        property: 'action',
                        type: 'string',
                        example: 'login',
                        enum: ['login', 'logout', 'page_view', 'api_call']
                    ),
                    new OA\Property(
                        property: 'metadata',
                        type: 'object',
                        example: ['browser' => 'Chrome', 'os' => 'macOS']
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: 'Activity log accepted for processing',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Activity logged'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function log(LogActivityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $userId = $request->attributes->get('user_id');
        if ($userId !== null && !$userId instanceof Uuid) {
            $userId = Uuid::fromString($userId);
        }

        $command = new LogActivityCommand(
            userId: $userId,
            url: $validated['url'],
            ipAddress: $validated['ip_address'],
            userAgent: $validated['user_agent'],
            action: $validated['action'],
            metadata: $validated['metadata'] ?? []
        );

        $this->logActivityHandler->handle($command);

        return $this->accepted(message: 'Activity logged');
    }
}
