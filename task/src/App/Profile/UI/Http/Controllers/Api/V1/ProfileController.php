<?php

declare(strict_types=1);

namespace App\Profile\UI\Http\Controllers\Api\V1;

use App\Profile\Application\Command\UpdateProfileCommand;
use App\Profile\Application\Command\UpdateProfileHandler;
use App\Profile\Application\Query\GetUserProfileHandler;
use App\Profile\Application\Query\GetUserProfileQuery;
use App\Profile\UI\Http\Mappers\ProfileMapper;
use App\Profile\UI\Http\Requests\UpdateProfileRequest;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class ProfileController extends ApiController
{
    public function __construct(
        private readonly GetUserProfileHandler $userProfileHandler,
        private readonly UpdateProfileHandler $updateProfileHandler,
    ) {
    }

    #[OA\Get(
        path: '/v1/me',
        summary: 'Get my Profile',
        tags: ['profile'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id'],
                properties: [
                    new OA\Property(property: 'id', type: 'string', example: ''),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Get My Profile'),
            new OA\Response(response: 400, description: 'Bad Request'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function show(string $userId): JsonResponse
    {
        $userProfile = $this->userProfileHandler->handle(new GetUserProfileQuery(
            userId: Uuid::fromString($userId),
        ));

        return $this->success($userProfile->toJson());
    }


    #[OA\Put(
        path: '/v1/me',
        summary: 'Update my Profile',
        tags: ['profile'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Update My Profile'),
            new OA\Response(response: 400, description: 'Bad Request'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $profileDTO = ProfileMapper::fromRequest($request);

            $this->updateProfileHandler->handle(new UpdateProfileCommand(
                profileDTO: $profileDTO
            ));
        } catch (\Exception $e) {
            return $this->error('Nie udało się zaktualizować profilu: ' . $e->getMessage(), 500);
        }

        return $this->noContent();
    }

}
