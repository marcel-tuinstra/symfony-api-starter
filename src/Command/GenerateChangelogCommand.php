<?php

namespace App\Command;

use App\Service\ChangelogGenerator;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:changelog:generate',
    description: 'Generate CHANGELOG.md from git history',
)]
class GenerateChangelogCommand extends Command
{
    public function __construct(
        private readonly ChangelogGenerator $changelogGenerator,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'since',
            null,
            InputOption::VALUE_REQUIRED,
            'Git reference to start from (defaults to latest tag, or all history if none found)',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $since = $input->getOption('since');

        $existingChangelog = null;
        $path = $this->projectDir . '/CHANGELOG.md';

        if (is_file($path)) {
            $existingChangelog = (string) file_get_contents($path);
        }

        try {
            $changelog = $this->changelogGenerator->generate($since, $existingChangelog);
        } catch (RuntimeException $exception) {
            $symfonyStyle->error($exception->getMessage());

            return Command::FAILURE;
        }

        $this->writeFile($changelog);

        $symfonyStyle->success('Changelog written to CHANGELOG.md');

        return Command::SUCCESS;
    }

    private function writeFile(string $contents): void
    {
        $path = $this->projectDir . '/CHANGELOG.md';

        file_put_contents($path, rtrim($contents) . PHP_EOL);
    }
}
