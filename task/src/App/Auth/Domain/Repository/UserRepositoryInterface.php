<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Application\DTO\UserDTO;
use App\Auth\Domain\Entity\User;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;

interface UserRepositoryInterface
{
    public function findById(Uuid $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function save(UserDTO $dto): void;

    public function exists(Email $email): bool;
}
