<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Ops;

use App\Ops\Application\Command\RecordDeployFailure\RecordDeployFailureCommand;
use App\Ops\Application\Command\RecordDeployFailure\RecordDeployFailureHandler;
use App\Ops\Domain\Entity\DeployFailure;
use App\Ops\Domain\Repository\DeployFailureRepositoryInterface;
use App\Ops\Domain\ValueObject\DeployContainerName;
use App\Ops\Domain\ValueObject\DeployHostname;
use App\Ops\Domain\ValueObject\DeployMessage;
use App\Ops\Domain\ValueObject\DeployProjectName;
use App\Ops\Domain\ValueObject\DeployRepository;
use App\Ops\Domain\ValueObject\DeployStage;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

final class RecordDeployFailureHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_saves_deploy_failure_and_returns_id(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $repository = Mockery::mock(DeployFailureRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (DeployFailure $failure) use ($uuid) {
                $failure->setId($uuid);

                return $failure->getProject()->getValue() === 'my-app'
                    && $failure->getRepository()->getValue() === 'org/repo'
                    && $failure->getContainer() === null
                    && $failure->getStage() === DeployStage::BUILD
                    && $failure->getMessage()->getValue() === 'build failed'
                    && $failure->getHostname() === null;
            }));

        $handler = new RecordDeployFailureHandler($repository);
        $command = new RecordDeployFailureCommand(
            DeployProjectName::fromString('my-app'),
            DeployRepository::fromString('org/repo'),
            null,
            DeployStage::BUILD,
            DeployMessage::fromString('build failed'),
            null,
        );

        $id = $handler->handle($command);

        $this->assertSame($uuid->getValue(), $id);
    }

    public function test_handle_persists_optional_container_and_hostname(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440001');

        $repository = Mockery::mock(DeployFailureRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (DeployFailure $failure) use ($uuid) {
                $failure->setId($uuid);

                return $failure->getContainer()?->getValue() === 'web'
                    && $failure->getHostname()?->getValue() === 'host.example';
            }));

        $handler = new RecordDeployFailureHandler($repository);
        $command = new RecordDeployFailureCommand(
            DeployProjectName::fromString('app'),
            DeployRepository::fromString('r/r'),
            DeployContainerName::fromString('web'),
            DeployStage::UP,
            DeployMessage::fromString('up'),
            DeployHostname::fromString('host.example'),
        );

        $this->assertSame($uuid->getValue(), $handler->handle($command));
    }

    public function test_handle_supports_status_stage(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440002');

        $repository = Mockery::mock(DeployFailureRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (DeployFailure $failure) use ($uuid) {
                $failure->setId($uuid);

                return $failure->getStage() === DeployStage::STATUS;
            }));

        $handler = new RecordDeployFailureHandler($repository);
        $command = new RecordDeployFailureCommand(
            DeployProjectName::fromString('p'),
            DeployRepository::fromString('r/r'),
            null,
            DeployStage::STATUS,
            DeployMessage::fromString('health check failed'),
            null,
        );

        $this->assertSame($uuid->getValue(), $handler->handle($command));
    }

    public function test_handle_accepts_max_length_message(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440003');
        $longText = str_repeat('x', 10000);

        $repository = Mockery::mock(DeployFailureRepositoryInterface::class);
        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (DeployFailure $failure) use ($uuid, $longText) {
                $failure->setId($uuid);

                return $failure->getMessage()->getValue() === $longText;
            }));

        $handler = new RecordDeployFailureHandler($repository);
        $command = new RecordDeployFailureCommand(
            DeployProjectName::fromString('p'),
            DeployRepository::fromString('r'),
            null,
            DeployStage::BUILD,
            DeployMessage::fromString($longText),
            null,
        );

        $this->assertSame($uuid->getValue(), $handler->handle($command));
    }
}
