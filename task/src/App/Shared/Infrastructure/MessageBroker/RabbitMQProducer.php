<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBroker;

use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMQProducer implements MessageProducerInterface
{
    public function __construct(
        private readonly RabbitMQConnection $connection
    ) {
    }

    public function publish(array $message, Queue $queue = Queue::COMMANDS): void
    {
        $queueName = $queue->value;

        $this->connection->declareQueue($queueName);

        $amqpMessage = new AMQPMessage(
            json_encode($message, JSON_THROW_ON_ERROR),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $this->connection->channel()->basic_publish(
            msg: $amqpMessage,
            routing_key: $queueName
        );
    }
}
