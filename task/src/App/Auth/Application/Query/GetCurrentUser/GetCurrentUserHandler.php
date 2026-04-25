<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetCurrentUser;

use App\Auth\Application\DTO\UserDTO;
use App\Auth\Domain\Exception\UserNotFoundException;
use App\Auth\Domain\Repository\UserRepositoryInterface;

final class GetCurrentUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function handle(GetCurrentUserQuery $query): UserDTO
    {
        $user = $this->userRepository->findById($query->userId);

        if (!$user) {
            throw UserNotFoundException::byId($query->userId);
        }

        return UserDTO::fromUser($user);
    }
}
