<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ChangelogGenerator;
use App\Tests\Unit\UnitTestCase;
use RuntimeException;

class ChangelogGeneratorTest extends UnitTestCase
{
    public function testGenerateAppendsExistingReleases(): void
    {
        $projectDir = $this->createTempGitRepo([
            [
                'message' => 'feat: add feature',
            ],
            [
                'message' => 'docs: add docs',
            ],
        ]);

        $existing = <<<TXT
            # Changelog
            All notable changes to this project are documented here.

            ## [Unreleased]
            - Pending changes since last release.

            ## [0.0.9] - 2024-01-01
            - Old release entry
            TXT;

        $generator = new ChangelogGenerator($projectDir);

        $result = $generator->generate(null, $existing);

        self::assertStringContainsString('## [Unreleased]', $result);
        self::assertStringContainsString('## [0.0.9] - 2024-01-01', $result, 'Existing release section preserved');
        self::assertStringContainsString('### Added', $result);
        self::assertStringContainsString('- add feature', $result);
        self::assertStringContainsString('### Docs', $result);
        self::assertStringContainsString('- add docs', $result);
    }

    public function testGenerateThrowsWhenGitFails(): void
    {
        $missingRepoDir = sys_get_temp_dir() . '/changelog_missing_' . uniqid('', true);
        mkdir($missingRepoDir, 0777, true);

        $generator = new ChangelogGenerator($missingRepoDir);

        $this->expectException(RuntimeException::class);
        $generator->generate();
    }

    /**
     * @param array<int, array{message: string}> $commits
     */
    private function createTempGitRepo(array $commits): string
    {
        $dir = sys_get_temp_dir() . '/changelog_' . uniqid('', true);
        mkdir($dir, 0777, true);

        $this->execInDir('git init', $dir);
        $this->execInDir('git config user.email "tester@example.com"', $dir);
        $this->execInDir('git config user.name "Tester"', $dir);

        foreach ($commits as $index => $commit) {
            $filename = $dir . '/file_' . $index . '.txt';
            file_put_contents($filename, 'content ' . $index);
            $this->execInDir(sprintf('git add %s', escapeshellarg($filename)), $dir);
            $this->execInDir(sprintf('git commit -m %s', escapeshellarg($commit['message'])), $dir);
        }

        return $dir;
    }

    private function execInDir(string $command, string $dir): void
    {
        $output = [];
        $code = 0;
        exec(sprintf('cd %s && %s', escapeshellarg($dir), $command), $output, $code);

        if ($code !== 0) {
            throw new RuntimeException(sprintf('Command failed: %s', $command));
        }
    }
}
