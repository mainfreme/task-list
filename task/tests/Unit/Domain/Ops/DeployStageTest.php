<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Ops;

use App\Ops\Domain\ValueObject\DeployStage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ValueError;

final class DeployStageTest extends TestCase
{
    public static function validStagesProvider(): array
    {
        return [
            'build' => ['build', DeployStage::BUILD],
            'up' => ['up', DeployStage::UP],
            'status' => ['status', DeployStage::STATUS],
        ];
    }

    #[DataProvider('validStagesProvider')]
    public function test_from_string_accepts_all_valid_values(string $raw, DeployStage $expected): void
    {
        $this->assertSame($expected, DeployStage::fromString($raw));
    }

    public function test_from_string_throws_on_invalid_value(): void
    {
        $this->expectException(ValueError::class);

        DeployStage::fromString('deploy');
    }
}
