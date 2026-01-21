<?php

declare(strict_types=1);

namespace App\Crm\Domain\ValueObject;

enum NoteType: string
{
    case NOTE = 'note';
    case CALL = 'call';
    case MEETING = 'meeting';
    case EMAIL = 'email';
    case TASK = 'task';

    public function label(): string
    {
        return match ($this) {
            self::NOTE => 'Note',
            self::CALL => 'Call',
            self::MEETING => 'Meeting',
            self::EMAIL => 'Email',
            self::TASK => 'Task',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
