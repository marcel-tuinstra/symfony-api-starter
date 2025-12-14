<?php

namespace App\Command;

use App\Service\ReleaseManager;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:release:finalize',
    description: 'Promote [Unreleased] changes into a versioned changelog section',
)]
class FinalizeReleaseCommand extends Command
{
    public function __construct(
        private readonly ReleaseManager $releaseManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'version',
            null,
            InputOption::VALUE_REQUIRED,
            'Version to tag (e.g., v0.1.1)',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $version = (string) $input->getOption('version');

        if ($version === '') {
            $symfonyStyle->error('Version is required (e.g., --version=v0.1.1).');

            return Command::FAILURE;
        }

        try {
            $this->releaseManager->finalizeChangelog($version);
        } catch (RuntimeException $exception) {
            $symfonyStyle->error($exception->getMessage());

            return Command::FAILURE;
        }

        $symfonyStyle->success(sprintf('Changelog finalized for %s. Remember to commit and tag.', $version));

        return Command::SUCCESS;
    }
}
