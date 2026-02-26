<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

use App\Auth\Application\DTO\UserDTO;

interface JwtTokenServiceInterface
{
    public function generateToken(UserDTO $user): string;

    public function getExpirationMinutes(): int;
}
