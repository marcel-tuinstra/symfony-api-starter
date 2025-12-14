<?php

namespace App\Service;

use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ChangelogGenerator
{
    private const array CATEGORY_MAP = [
        'feat' => 'Added',
        'fix' => 'Fixed',
        'docs' => 'Docs',
        'refactor' => 'Changed',
        'perf' => 'Changed',
        'test' => 'Tests',
        'chore' => 'Chore',
        'build' => 'Chore',
        'ci' => 'Chore',
        'style' => 'Chore',
        'revert' => 'Reverted',
    ];

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function generate(?string $sinceReference = null, ?string $existingChangelog = null): string
    {
        $sinceReference ??= $this->findLatestTag();

        $commits = $this->getCommitMessages($sinceReference);
        $sections = $this->groupByCategory($commits);

        $existingBody = $existingChangelog !== null ? $this->stripUnreleased($this->removeHeader($existingChangelog)) : '';

        $newContent = $this->render($sections, $sinceReference);

        if ($existingBody === '') {
            return $newContent;
        }

        return rtrim($newContent) . PHP_EOL . PHP_EOL . ltrim($existingBody);
    }

    private function findLatestTag(): ?string
    {
        $command = 'git describe --tags --abbrev=0 2>/dev/null';
        [$output, $exitCode] = $this->runGitCommand($command);

        if ($exitCode !== 0 || $output === []) {
            return null;
        }

        return trim($output[0]);
    }

    /**
     * @return string[]
     */
    private function getCommitMessages(?string $sinceReference): array
    {
        $range = $sinceReference !== null ? sprintf('%s..HEAD', $sinceReference) : '';
        $command = 'git log --no-merges --pretty=format:%s';

        if ($range !== '') {
            $command .= ' ' . escapeshellarg($range);
        }

        [$output, $exitCode] = $this->runGitCommand($command);

        if ($exitCode !== 0) {
            throw new RuntimeException('Git log command failed; ensure git is installed and the repository has history.');
        }

        return $output;
    }

    /**
     * @param string[] $commitMessages
     *
     * @return array<string, string[]>
     */
    private function groupByCategory(array $commitMessages): array
    {
        $sections = [];

        foreach ($commitMessages as $commitMessage) {
            $parsed = $this->parseCommitMessage($commitMessage);
            $category = $this->mapCategory($parsed['type']);
            $sections[$category][] = $parsed['message'];
        }

        foreach ($sections as $category => $messages) {
            $sections[$category] = array_values(array_unique($messages));
        }

        return $sections;
    }

    /**
     * @return array{type: string, message: string}
     */
    private function parseCommitMessage(string $message): array
    {
        $pattern = '/^(?<type>[a-z]+)(?:\((?<scope>[^)]+)\))?:\s*(?<subject>.+)$/i';
        $matches = [];

        if (preg_match($pattern, $message, $matches) === 1) {
            $scope = trim($matches['scope']);
            $summary = trim($matches['subject']);
            $type = strtolower($matches['type']);

            if ($scope !== '') {
                return [
                    'type' => $type,
                    'message' => sprintf('%s: %s', $scope, $summary),
                ];
            }

            return [
                'type' => $type,
                'message' => $summary,
            ];
        }

        return [
            'type' => 'other',
            'message' => trim($message),
        ];
    }

    private function mapCategory(string $type): string
    {
        return self::CATEGORY_MAP[$type] ?? 'Other';
    }

    /**
     * @param array<string, string[]> $sections
     */
    private function render(array $sections, ?string $sinceReference): string
    {
        $lines = [
            '# Changelog',
            'All notable changes to this project will be documented in this file.',
            '',
            '## [Unreleased]',
            sprintf('- Generated on %s', (new DateTimeImmutable())->format('Y-m-d')),
            $sinceReference !== null ? sprintf('- Changes since %s', $sinceReference) : '- Changes since initial commit',
            '',
        ];

        if ($sections === []) {
            $lines[] = '_No notable changes found._';

            return implode(PHP_EOL, $lines) . PHP_EOL;
        }

        $orderedSections = $this->orderSections($sections);

        foreach ($orderedSections as $category => $messages) {
            $lines[] = sprintf('### %s', $category);

            foreach ($messages as $message) {
                $lines[] = sprintf('- %s', $message);
            }

            $lines[] = '';
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param array<string, string[]> $sections
     *
     * @return array<string, string[]>
     */
    private function orderSections(array $sections): array
    {
        $orderedKeys = ['Added', 'Changed', 'Fixed', 'Docs', 'Tests', 'Chore', 'Reverted', 'Other'];
        $orderedSections = [];

        foreach ($orderedKeys as $key) {
            if (isset($sections[$key])) {
                $orderedSections[$key] = $sections[$key];
            }
        }

        foreach ($sections as $key => $messages) {
            if (! isset($orderedSections[$key])) {
                $orderedSections[$key] = $messages;
            }
        }

        return $orderedSections;
    }

    private function stripUnreleased(string $changelog): string
    {
        $start = stripos($changelog, '## [Unreleased]');

        if ($start === false) {
            return $changelog;
        }

        $afterStart = $start + strlen('## [Unreleased]');
        $nextSection = stripos($changelog, '## [', $afterStart);

        if ($nextSection === false) {
            return substr($changelog, 0, $start);
        }

        return substr($changelog, 0, $start) . substr($changelog, $nextSection);
    }

    private function removeHeader(string $changelog): string
    {
        $lines = preg_split('/\\r?\\n/', $changelog) ?: [];

        if (isset($lines[0]) && trim($lines[0]) === '# Changelog') {
            array_shift($lines);
        }

        if (isset($lines[0]) && str_starts_with(trim($lines[0]), 'All notable changes')) {
            array_shift($lines);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @return array{0: string[], 1: int}
     */
    private function runGitCommand(string $command): array
    {
        $fullCommand = sprintf('cd %s && %s', escapeshellarg($this->projectDir), $command);
        $output = [];
        $exitCode = 0;

        exec($fullCommand, $output, $exitCode);

        return [$output, $exitCode];
    }
}
