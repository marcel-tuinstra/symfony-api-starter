<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ReleaseManager;
use App\Tests\Unit\UnitTestCase;
use DateTimeImmutable;

class ReleaseManagerTest extends UnitTestCase
{
    public function testFinalizePromotesUnreleasedToVersion(): void
    {
        $dir = sys_get_temp_dir() . '/release_' . uniqid('', true);
        mkdir($dir, 0777, true);

        $changelog = <<<TXT
            # Changelog
            All notable changes to this project are documented here.

            ## [Unreleased]
            - Added new feature
            - Fixed bug

            ## [0.0.9] - 2024-01-01
            - Previous release
            TXT;

        file_put_contents($dir . '/CHANGELOG.md', $changelog);

        $manager = new ReleaseManager($dir);
        $manager->finalizeChangelog('v0.1.0', new DateTimeImmutable('2025-12-14'));

        $result = (string) file_get_contents($dir . '/CHANGELOG.md');

        self::assertStringContainsString('## [Unreleased]' . PHP_EOL . '- Pending changes since last release.', $result);
        self::assertStringContainsString('## [v0.1.0] - 2025-12-14', $result);
        self::assertStringContainsString('- Added new feature', $result);
        self::assertStringContainsString('- Fixed bug', $result);
        self::assertStringContainsString('## [0.0.9] - 2024-01-01', $result);
    }
}
