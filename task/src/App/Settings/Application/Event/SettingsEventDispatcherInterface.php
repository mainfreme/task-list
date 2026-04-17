<?php

declare(strict_types=1);

namespace App\Settings\Application\Event;

use App\Settings\Domain\Event\SettingsChangedEvent;

interface SettingsEventDispatcherInterface
{
    public function dispatch(SettingsChangedEvent $event): void;
}
