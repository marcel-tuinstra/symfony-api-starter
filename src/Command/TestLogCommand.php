<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test:log',
    description: 'Add a short description for your command',
)]
class TestLogCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new SymfonyStyle($input, $output);

        $this->logger->info('✅ Monolog test — info level reached');
        $this->logger->error('❌ Monolog test — error level reached');
        $this->logger->info('🪵 Sentry test — log entry should appear as breadcrumb');

        throw new \RuntimeException('💥 Sentry test exception');

        // return Command::SUCCESS; // unreachable if throwing, but required by signature
    }
}
