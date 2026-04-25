<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\LoginUser;

use App\Auth\Application\DTO\AuthResponseDTO;
use App\Auth\Application\DTO\UserDTO;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Service\JwtTokenServiceInterface;
use App\Auth\Domain\ValueObject\UserIdentity;

final class LoginUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly JwtTokenServiceInterface $jwtTokenService,
    ) {
    }

    public function handle(LoginUserCommand $command): AuthResponseDTO
    {
        $user = $this->userRepository->findByEmail($command->email);

        if (!$user || !$user->verifyPassword($command->password)) {
            throw InvalidCredentialsException::create();
        }

        $userDto = UserDTO::fromUser($user);
        $token = $this->jwtTokenService->generateToken(UserIdentity::fromUser($user));

        return new AuthResponseDTO(
            user: $userDto,
            token: $token,
            expiresIn: $this->jwtTokenService->getExpirationMinutes(),
        );
    }
}
