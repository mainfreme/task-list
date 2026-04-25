<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Cache\SettingsQueryCacheInterface;
use App\Settings\Application\Mapper\SettingsEntityMapper;
use App\Settings\Application\Query\ListIntegrationAccounts\ListIntegrationAccountsHandler;
use App\Settings\Application\Query\ListIntegrationAccounts\ListIntegrationAccountsQuery;
use App\Settings\Application\Service\IntegrationCredentialsMasker;
use App\Settings\Domain\Entity\IntegrationAccount;
use App\Settings\Domain\Repository\IntegrationAccountRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ListIntegrationAccountsHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_returns_summaries_with_masked_credentials(): void
    {
        $id = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $account = IntegrationAccount::reconstitute(
            id: $id,
            name: 'Test',
            enabled: true,
            externalAccountId: 'ext-1',
            provider: 'facebook',
            credentials: [
                'accessToken' => 'SECRET',
                'pageId' => '99',
            ],
            createdAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
        );

        $repository = Mockery::mock(IntegrationAccountRepositoryInterface::class);
        $cache = Mockery::mock(SettingsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(null);
        $cache->shouldReceive('save')->once();
        $repository->shouldReceive('findAll')->once()->andReturn([$account]);

        $handler = new ListIntegrationAccountsHandler(
            $repository,
            new SettingsEntityMapper(new IntegrationCredentialsMasker()),
            $cache
        );

        $items = $handler->handle(new ListIntegrationAccountsQuery());

        $this->assertCount(1, $items);
        $row = $items[0]->toArray();
        $this->assertSame('SECRET', $account->getCredentials()['accessToken']);
        $this->assertSame('••••••••', $row['credentials']['accessToken']);
        $this->assertSame('99', $row['credentials']['pageId']);
    }

    public function test_handle_returns_empty_array_when_cache_payload_items_is_not_array(): void
    {
        $repository = Mockery::mock(IntegrationAccountRepositoryInterface::class);
        $cache = Mockery::mock(SettingsQueryCacheInterface::class);
        $cache->shouldReceive('find')->once()->andReturn(['items' => 'invalid-shape']);
        $repository->shouldNotReceive('findAll');
        $cache->shouldNotReceive('save');

        $handler = new ListIntegrationAccountsHandler(
            $repository,
            new SettingsEntityMapper(new IntegrationCredentialsMasker()),
            $cache
        );

        $items = $handler->handle(new ListIntegrationAccountsQuery());

        $this->assertSame([], $items);
    }
}
