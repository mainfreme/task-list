<?php   

declare(strict_types=1);

namespace App\Auth\Domain\Enum;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case MODERATOR = 'moderator';
}