<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Event;

use App\Settings\Application\Event\SettingsEventDispatcherInterface;
use App\Settings\Domain\Event\SettingsChangedEvent;

final class LaravelSettingsEventDispatcher implements SettingsEventDispatcherInterface
{
    public function dispatch(SettingsChangedEvent $event): void
    {
        event($event);
    }
}
