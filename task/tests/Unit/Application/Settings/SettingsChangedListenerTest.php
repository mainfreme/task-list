<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Auth\Domain\Service\ActivityLogProducerInterface;
use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Domain\Event\SettingsChangedEvent;
use App\Settings\Infrastructure\Listener\SettingsChangedListener;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class SettingsChangedListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_invalidates_cache_even_when_actor_id_is_invalid_uuid(): void
    {
        $cache = Mockery::mock(SettingsQueryCacheInterface::class);
        $producer = Mockery::mock(ActivityLogProducerInterface::class);
        $logger = Mockery::mock(LoggerInterface::class);

        $cache->shouldReceive('invalidate')->once();
        $producer->shouldReceive('log')->never();
        $logger->shouldReceive('error')
            ->once()
            ->with(
                Mockery::type('string'),
                Mockery::on(fn (array $context) => ($context['resource_type'] ?? null) === 'setting_entry')
            );

        $listener = new SettingsChangedListener($cache, $producer, $logger);
        $listener->handle(new SettingsChangedEvent(
            resourceType: 'setting_entry',
            resourceId: '550e8400-e29b-41d4-a716-446655440000',
            operation: SettingsChangedEvent::OPERATION_UPDATED,
            before: ['value' => 'a'],
            after: ['value' => 'b'],
            changedFields: ['value'],
            actorId: 'not-a-uuid'
        ));

        $this->assertTrue(true);
    }

    public function test_handle_logs_with_fallback_request_metadata_when_missing(): void
    {
        $cache = Mockery::mock(SettingsQueryCacheInterface::class);
        $producer = Mockery::mock(ActivityLogProducerInterface::class);
        $logger = Mockery::mock(LoggerInterface::class);

        $cache->shouldReceive('invalidate')->once();
        $logger->shouldNotReceive('error');
        $producer->shouldReceive('log')
            ->once()
            ->withArgs(function (
                $userId,
                string $url,
                string $ipAddress,
                string $userAgent,
                string $action,
                array $metadata
            ): bool {
                return $userId === null
                    && $url === '/api/v1/settings'
                    && $ipAddress === '0.0.0.0'
                    && $userAgent === 'settings-events-listener'
                    && $action === 'settings.chart_definition.deleted'
                    && ($metadata['operation'] ?? null) === SettingsChangedEvent::OPERATION_DELETED;
            });

        $listener = new SettingsChangedListener($cache, $producer, $logger);
        $listener->handle(new SettingsChangedEvent(
            resourceType: 'chart_definition',
            resourceId: '550e8400-e29b-41d4-a716-446655440000',
            operation: SettingsChangedEvent::OPERATION_DELETED,
            before: ['sql_query' => 'SELECT 1'],
            after: null,
            changedFields: ['sql_query']
        ));

        $this->assertTrue(true);
    }
}
