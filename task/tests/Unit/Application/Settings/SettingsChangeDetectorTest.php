<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Settings;

use App\Settings\Application\Command\SettingsChangeDetector;
use PHPUnit\Framework\TestCase;

final class SettingsChangeDetectorTest extends TestCase
{
    public function test_changed_fields_handles_null_vs_empty_as_no_change(): void
    {
        $changed = SettingsChangeDetector::changedFields(
            before: null,
            after: []
        );

        $this->assertSame([], $changed);
    }

    public function test_changed_fields_detects_nested_structure_changes_as_top_level_key(): void
    {
        $changed = SettingsChangeDetector::changedFields(
            before: ['credentials' => ['token' => 'old', 'region' => 'eu']],
            after: ['credentials' => ['token' => 'new', 'region' => 'eu']]
        );

        $this->assertSame(['credentials'], $changed);
    }
}
