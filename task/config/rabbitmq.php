<?php

declare(strict_types=1);

use App\Shared\Infrastructure\MessageBroker\Queue;

return [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => (int) env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'password' => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),

    'default_queue' => Queue::COMMANDS->value,

    'queues' => [
        Queue::COMMANDS->value => [
            'durable' => true,
        ],
        Queue::ACTIVITY_LOGS->value => [
            'durable' => true,
        ],
    ],
];
