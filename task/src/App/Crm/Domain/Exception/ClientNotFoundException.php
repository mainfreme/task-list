<?php

declare(strict_types=1);

namespace App\Crm\Domain\Exception;

use Exception;

final class ClientNotFoundException extends Exception
{
    public static function byId(string $id): self
    {
        return new self("Client with ID {$id} not found");
    }
}
