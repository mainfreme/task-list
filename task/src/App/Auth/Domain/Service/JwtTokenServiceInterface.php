<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

use App\Auth\Domain\ValueObject\UserIdentity;

interface JwtTokenServiceInterface
{
    public function generateToken(UserIdentity $identity): string;

    public function getExpirationMinutes(): int;
}
