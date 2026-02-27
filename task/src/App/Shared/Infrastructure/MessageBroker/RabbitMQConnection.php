<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBroker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class RabbitMQConnection
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $user,
        private readonly string $password,
        private readonly string $vhost
    ) {
    }

    public function connect(): void
    {
        if ($this->connection === null || !$this->connection->isConnected()) {
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );
        }
    }

    public function channel(): AMQPChannel
    {
        $this->connect();

        if ($this->channel === null || !$this->channel->is_open()) {
            $this->channel = $this->connection->channel();
        }

        return $this->channel;
    }

    public function declareQueue(string $queue, bool $durable = true): void
    {
        $this->channel()->queue_declare(
            queue: $queue,
            passive: false,
            durable: $durable,
            exclusive: false,
            auto_delete: false
        );
    }

    public function disconnect(): void
    {
        if ($this->channel !== null && $this->channel->is_open()) {
            $this->channel->close();
        }

        if ($this->connection !== null && $this->connection->isConnected()) {
            $this->connection->close();
        }

        $this->channel = null;
        $this->connection = null;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
