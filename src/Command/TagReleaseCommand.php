<?php

namespace App\Command;

use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:release:tag',
    description: 'Create a git tag for the given version (lightweight helper, does not push)',
)]
class TagReleaseCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption(
            'version',
            null,
            InputOption::VALUE_REQUIRED,
            'Version to tag (e.g., v0.1.1)',
        );

        $this->addOption(
            'message',
            null,
            InputOption::VALUE_REQUIRED,
            'Tag message',
            null
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $version = (string) $input->getOption('version');
        $message = $input->getOption('message') ?? sprintf('Release %s', $version);

        if ($version === '') {
            $io->error('Version is required (e.g., --version=v0.1.1).');

            return Command::FAILURE;
        }

        if (! $this->isCleanWorkingTree()) {
            $io->error('Working tree is not clean. Commit or stash changes before tagging.');

            return Command::FAILURE;
        }

        $process = new Process(['git', 'tag', '-a', $version, '-m', $message]);
        $process->run();

        if (! $process->isSuccessful()) {
            $io->error(trim($process->getErrorOutput()) ?: 'Failed to create tag.');

            return Command::FAILURE;
        }

        $io->success(sprintf('Created tag %s. Push with: git push origin %s', $version, $version));

        return Command::SUCCESS;
    }

    private function isCleanWorkingTree(): bool
    {
        $process = new Process(['git', 'status', '--porcelain']);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('Failed to determine git status.');
        }

        return trim($process->getOutput()) === '';
    }
}
