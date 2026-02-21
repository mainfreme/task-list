<?php

declare(strict_types=1);

namespace App\Profile\Application\Command;

use App\Profile\Application\DTO\ProfileDTO;

final class UpdateProfileCommand
{
    public function __construct(
        public readonly ProfileDTO $profileDTO,
    ) {
    }
}