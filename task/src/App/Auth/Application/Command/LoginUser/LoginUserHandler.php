<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\LoginUser;

use App\Auth\Application\DTO\AuthResponseDTO;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Infrastructure\Service\JwtTokenService;

final class LoginUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly JwtTokenService $jwtTokenService,
    ) {
    }

    public function handle(LoginUserCommand $command): AuthResponseDTO
    {
        $user = $this->userRepository->findByEmail($command->email);

        if (!$user || !$user->verifyPassword($command->password)) {
            throw InvalidCredentialsException::create();
        }

        $userDto = $user->toDTO();
        $token = $this->jwtTokenService->generateToken($userDto);

        return new AuthResponseDTO(
            user: $userDto,
            token: $token,
            expiresIn: $this->jwtTokenService->getExpirationMinutes(),
        );
    }
}
