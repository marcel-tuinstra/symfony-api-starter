<?php

namespace App\Service;

use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ReleaseManager
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function finalizeChangelog(string $version, ?DateTimeImmutable $date = null): void
    {
        $path = $this->projectDir . '/CHANGELOG.md';

        if (! is_file($path)) {
            throw new RuntimeException('CHANGELOG.md not found. Generate it first with app:changelog:generate.');
        }

        $contents = (string) file_get_contents($path);
        $dateLabel = ($date ?? new DateTimeImmutable())->format('Y-m-d');

        $updated = $this->promoteUnreleased($contents, $version, $dateLabel);

        file_put_contents($path, $updated);
    }

    private function promoteUnreleased(string $contents, string $version, string $dateLabel): string
    {
        $unreleasedPos = stripos($contents, '## [Unreleased]');

        if ($unreleasedPos === false) {
            throw new RuntimeException('No [Unreleased] section found in CHANGELOG.md.');
        }

        $afterUnreleased = $unreleasedPos + strlen('## [Unreleased]');
        $nextSection = stripos($contents, '## [', $afterUnreleased);
        $unreleasedBody = '';
        $tail = '';

        if ($nextSection === false) {
            $unreleasedBody = trim(substr($contents, $afterUnreleased));
        } else {
            $unreleasedBody = trim(substr($contents, $afterUnreleased, $nextSection - $afterUnreleased));
            $tail = substr($contents, $nextSection);
        }

        $header = trim(substr($contents, 0, $unreleasedPos));

        $newUnreleased = "## [Unreleased]\n- Pending changes since last release.\n\n";
        $releaseSection = sprintf("## [%s] - %s\n%s\n\n", $version, $dateLabel, $unreleasedBody !== '' ? $unreleasedBody : '- (no notable changes listed)');

        return rtrim($header) . "\n\n" . $newUnreleased . $releaseSection . ltrim($tail);
    }
}
