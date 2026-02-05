<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RegisterUser;

use App\Auth\Application\DTO\AuthResponseDTO;
use App\Auth\Application\DTO\UserDTO;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Exception\EmailAlreadyExistsException;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Infrastructure\Service\JwtTokenService;

final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly JwtTokenService $jwtTokenService,
    ) {
    }

    public function handle(RegisterUserCommand $command): AuthResponseDTO
    {
        if ($this->userRepository->exists($command->email)) {
            throw EmailAlreadyExistsException::create($command->email);
        }

        $user = User::create(
            name: $command->name,
            email: $command->email,
            password: $command->password,
        );

        $userDto = $user->toDTO();
        $this->userRepository->save($userDto);

        $token = $this->jwtTokenService->generateToken($userDto);

        return new AuthResponseDTO(
            user: $userDto,
            token: $token,
            expiresIn: $this->jwtTokenService->getExpirationMinutes(),
        );
    }
}
