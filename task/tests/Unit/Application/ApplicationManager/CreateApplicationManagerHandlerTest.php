<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ApplicationManager;

use App\ApplicationManager\Application\Command\CreateApplicationManager\CreateApplicationManagerCommand;
use App\ApplicationManager\Application\Command\CreateApplicationManager\CreateApplicationManagerHandler;
use App\ApplicationManager\Application\DTO\ApplicationManagerDTO;
use App\ApplicationManager\Domain\Entity\ApplicationManager;
use App\ApplicationManager\Domain\Repository\ApplicationManagerRepositoryInterface;
use App\ApplicationManager\Domain\ValueObject\ApplicationName;
use App\ApplicationManager\Domain\ValueObject\IpWhitelist;
use App\ApplicationManager\Domain\ValueObject\RequestUrl;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class CreateApplicationManagerHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_application_manager_and_saves(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (ApplicationManager $am) use ($uuid) {
                $am->setId($uuid);
                return $am->getName()->getValue() === 'Nowa Aplikacja';
            }));

        $handler = new CreateApplicationManagerHandler($repository);
        $command = new CreateApplicationManagerCommand(
            ApplicationName::fromString('Nowa Aplikacja')
        );

        $result = $handler->handle($command);

        $this->assertInstanceOf(ApplicationManagerDTO::class, $result);
        $this->assertSame($uuid->getValue(), $result->id->getValue());
        $this->assertSame('Nowa Aplikacja', $result->name->getValue());
        $this->assertTrue($result->isActive);
        $this->assertNotNull($result->apiKeyHash);
        $this->assertSame(64, strlen($result->apiKeyHash->value()));
    }

    public function test_handle_creates_inactive_application_when_specified(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (ApplicationManager $am) use ($uuid) {
                $am->setId($uuid);
                return !$am->isActive();
            }));

        $handler = new CreateApplicationManagerHandler($repository);
        $command = new CreateApplicationManagerCommand(
            ApplicationName::fromString('Nieaktywna'),
            null,
            false
        );

        $result = $handler->handle($command);

        $this->assertFalse($result->isActive);
    }

    public function test_handle_creates_with_request_url_and_ip_whitelist(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $requestUrl = RequestUrl::fromString('https://callback.example.com');
        $ipWhitelist = IpWhitelist::fromArray(['192.168.1.0/24']);

        $repository = Mockery::mock(ApplicationManagerRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (ApplicationManager $am) use ($uuid) {
                $am->setId($uuid);
                return $am->getRequestUrl() !== null && $am->getIpWhitelist() !== null;
            }));

        $handler = new CreateApplicationManagerHandler($repository);
        $command = new CreateApplicationManagerCommand(
            ApplicationName::fromString('Pełna konfiguracja'),
            $requestUrl,
            true,
            $ipWhitelist
        );

        $result = $handler->handle($command);

        $this->assertNotNull($result->requestUrl);
        $this->assertSame('https://callback.example.com', $result->requestUrl->getValue());
        $this->assertNotNull($result->ipWhitelist);
        $this->assertTrue($result->ipWhitelist->allows('192.168.1.100'));
    }
}
