<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Console;

use App\Auth\Domain\Entity\ActivityUserLog;
use App\Auth\Domain\Repository\ActivityLogRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\MessageBroker\Queue;
use App\Shared\Infrastructure\MessageBroker\RabbitMQConnection;
use Illuminate\Console\Command;

final class ActivityLogConsumerCommand extends Command
{
    protected $signature = 'activity:consume {--timeout=0 : Stop after timeout seconds (0 = run forever)}';

    protected $description = 'Consume activity log messages from RabbitMQ queue';

    public function handle(
        RabbitMQConnection $connection,
        ActivityLogRepositoryInterface $repository
    ): int {
        $this->info('Starting activity log consumer...');

        $timeout = (int) $this->option('timeout');
        $startTime = time();
        $queueName = Queue::ACTIVITY_LOGS->value;

        try {
            $connection->declareQueue($queueName);
            $channel = $connection->channel();

            $callback = function ($msg) use ($repository): void {
                try {
                    $data = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);

                    $entity = ActivityUserLog::create(
                        userId: isset($data['user_id']) ? Uuid::fromString($data['user_id']) : null,
                        url: $data['url'] ?? null,
                        logActivity: $data['log_activity'] ?? []
                    );

                    $repository->save($entity);

                    $this->info(sprintf(
                        '[%s] Processed activity log for user: %s',
                        date('Y-m-d H:i:s'),
                        $data['user_id'] ?? 'anonymous'
                    ));

                    $msg->ack();
                } catch (\Throwable $e) {
                    $this->error('Failed to process message: ' . $e->getMessage());
                    $msg->nack(requeue: true);
                }
            };

            $channel->basic_qos(prefetch_size: 0, prefetch_count: 1, a_global: false);
            $channel->basic_consume(
                queue: $queueName,
                consumer_tag: '',
                no_local: false,
                no_ack: false,
                exclusive: false,
                nowait: false,
                callback: $callback
            );

            $this->info('Waiting for messages. Press Ctrl+C to exit.');

            while ($channel->is_consuming()) {
                if ($timeout > 0 && (time() - $startTime) >= $timeout) {
                    $this->info('Timeout reached. Stopping consumer.');
                    break;
                }

                $channel->wait(timeout: 1);
            }
        } catch (\Throwable $e) {
            $this->error('Consumer error: ' . $e->getMessage());

            return Command::FAILURE;
        } finally {
            $connection->disconnect();
        }

        return Command::SUCCESS;
    }
}
