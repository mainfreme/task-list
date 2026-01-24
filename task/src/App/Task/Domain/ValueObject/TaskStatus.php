<?php

declare(strict_types=1);

namespace App\Task\Domain\ValueObject;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Oczekuje na realizację',
            self::IN_PROGRESS => 'W trakcie realizacji',
            self::COMPLETED => 'Zakończono',
            self::CANCELLED => 'Anulowano',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
