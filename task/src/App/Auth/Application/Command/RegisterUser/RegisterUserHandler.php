<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RegisterUser;

use App\Auth\Application\DTO\AuthResponseDTO;
use App\Auth\Application\DTO\UserDTO;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Exception\EmailAlreadyExistsException;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Service\JwtTokenServiceInterface;
use App\Auth\Domain\ValueObject\UserIdentity;

final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly JwtTokenServiceInterface $jwtTokenService,
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

        $this->userRepository->save($user);

        $userDto = UserDTO::fromUser($user);
        $token = $this->jwtTokenService->generateToken(UserIdentity::fromUser($user));

        return new AuthResponseDTO(
            user: $userDto,
            token: $token,
            expiresIn: $this->jwtTokenService->getExpirationMinutes(),
        );
    }
}
