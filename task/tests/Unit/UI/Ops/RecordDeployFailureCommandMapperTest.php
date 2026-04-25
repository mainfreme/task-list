<?php

declare(strict_types=1);

namespace Tests\Unit\UI\Ops;

use App\Ops\Domain\ValueObject\DeployStage;
use App\Ops\UI\Http\Mappers\RecordDeployFailureCommandMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RecordDeployFailureCommandMapperTest extends TestCase
{
    public static function stageProvider(): array
    {
        return [
            'build' => ['build', DeployStage::BUILD],
            'up' => ['up', DeployStage::UP],
            'status' => ['status', DeployStage::STATUS],
        ];
    }

    #[DataProvider('stageProvider')]
    public function test_maps_all_stages(string $stage, DeployStage $expected): void
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray([
            'project' => 'p',
            'repository' => 'a/b',
            'stage' => $stage,
            'message' => 'm',
        ]);

        $this->assertSame($expected, $command->stage);
    }

    public function test_prefers_message_when_both_message_and_error_present(): void
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray([
            'project' => 'p',
            'repository' => 'a/b',
            'stage' => 'build',
            'message' => 'from-message',
            'error' => 'from-error',
        ]);

        $this->assertSame('from-message', $command->message->getValue());
    }

    public function test_uses_error_when_message_key_missing(): void
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray([
            'project' => 'p',
            'repository' => 'a/b',
            'stage' => 'up',
            'error' => 'only-error',
        ]);

        $this->assertSame('only-error', $command->message->getValue());
    }

    public function test_empty_string_message_does_not_fallback_to_error(): void
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray([
            'project' => 'p',
            'repository' => 'a/b',
            'stage' => 'status',
            'message' => '',
            'error' => 'fallback',
        ]);

        $this->assertSame('', $command->message->getValue());
    }

    public function test_container_empty_string_maps_to_null(): void
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray([
            'project' => 'p',
            'repository' => 'a/b',
            'stage' => 'build',
            'message' => 'x',
            'container' => '',
        ]);

        $this->assertNull($command->container);
    }

    public function test_hostname_empty_string_maps_to_null(): void
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray([
            'project' => 'p',
            'repository' => 'a/b',
            'stage' => 'build',
            'message' => 'x',
            'hostname' => '',
        ]);

        $this->assertNull($command->hostname);
    }

    public function test_optional_container_and_hostname_omitted_are_null(): void
    {
        $command = RecordDeployFailureCommandMapper::fromValidatedArray([
            'project' => 'p',
            'repository' => 'a/b',
            'stage' => 'build',
            'message' => 'x',
        ]);

        $this->assertNull($command->container);
        $this->assertNull($command->hostname);
    }
}
