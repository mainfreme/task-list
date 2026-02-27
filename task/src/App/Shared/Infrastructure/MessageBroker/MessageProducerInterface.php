<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBroker;

interface MessageProducerInterface
{
    public function publish(array $message, Queue $queue = Queue::COMMANDS): void;
}
