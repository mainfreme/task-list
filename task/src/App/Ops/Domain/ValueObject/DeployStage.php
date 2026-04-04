<?php

declare(strict_types=1);

namespace App\Ops\Domain\ValueObject;

enum DeployStage: string
{
    case BUILD = 'build';
    case UP = 'up';
    case STATUS = 'status';

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
