<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBroker;

enum Queue: string
{
    case COMMANDS = 'commands';

    case ACTIVITY_LOGS = 'activity_logs';
}
