<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controllers\Api\V1;

use App\Auth\Application\Command\LoginUser\LoginUserCommand;
use App\Auth\Application\Command\LoginUser\LoginUserHandler;
use App\Auth\Application\Command\RegisterUser\RegisterUserCommand;
use App\Auth\Application\Command\RegisterUser\RegisterUserHandler;
use App\Auth\Application\Query\GetCurrentUser\GetCurrentUserHandler;
use App\Auth\Application\Query\GetCurrentUser\GetCurrentUserQuery;
use App\Auth\Domain\Exception\EmailAlreadyExistsException;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\UI\Http\Requests\V1\LoginRequest;
use App\Auth\UI\Http\Requests\V1\RegisterRequest;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\UI\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Authentication", description: "User authentication endpoints")]
final class AuthController extends ApiController
{
    public function __construct(
        private readonly RegisterUserHandler $registerHandler,
        private readonly LoginUserHandler $loginHandler,
        private readonly GetCurrentUserHandler $getCurrentUserHandler,
    ) {
    }

    #[OA\Post(
        path: "/v1/auth/register",
        summary: "Register a new user",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Jan Kowalski"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "jan@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User registered successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User registered successfully"),
                        new OA\Property(property: "user", type: "object"),
                        new OA\Property(property: "token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "expires_in", type: "integer", example: 1440),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 409, description: "Email already exists")
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $command = new RegisterUserCommand(
            name: $validated['name'],
            email: Email::fromString($validated['email']),
            password: $validated['password'],
        );

        try {
            $response = $this->registerHandler->handle($command);
        } catch (EmailAlreadyExistsException) {
            return $this->conflict(message: 'Email already registered');
        }

        return $this->created($response->toArray());
    }

    #[OA\Post(
        path: "/v1/auth/login",
        summary: "Login user",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "jan@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login successful"),
                        new OA\Property(property: "user", type: "object"),
                        new OA\Property(property: "token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "expires_in", type: "integer", example: 1440),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $command = new LoginUserCommand(
            email: Email::fromString($validated['email']),
            password: $validated['password'],
        );

        try {
            $response = $this->loginHandler->handle($command);
        } catch (InvalidCredentialsException) {
            return $this->unauthorized(message: 'Invalid email or password');
        }

        return $this->success($response->toArray());
    }

    #[OA\Post(
        path: "/v1/auth/logout",
        summary: "Logout user",
        tags: ["Authentication"],
        security: [["user_jwt" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Logout successful"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        // Since we're using stateless JWT, logout is handled on client side
        // by removing the 

        return $this->success(
            message: 'Logout successful',
        );
    }

    #[OA\Get(
        path: "/v1/auth/me",
        summary: "Get current user data",
        tags: ["Authentication"],
        security: [["user_jwt" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "User data",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        $userId = $request->attributes->get('user_id');

        $query = new GetCurrentUserQuery(
            userId: Uuid::fromString($userId),
        );

        $userDto = $this->getCurrentUserHandler->handle($query);

        return $this->success([
            'user' => $userDto->toArray(),
        ]);
    }
}
