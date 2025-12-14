<?php

namespace App\Command;

use App\Service\TestMailer;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mail:test',
    description: 'Send a test email using the configured mailer transport (Mailpit by default)',
)]
class TestMailCommand extends Command
{
    public function __construct(
        private readonly TestMailer $testMailer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'to',
            null,
            InputOption::VALUE_REQUIRED,
            'Recipient email address',
            'dev@example.com',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $recipient = $input->getOption('to');

        if (! is_string($recipient) || trim($recipient) === '') {
            throw new InvalidArgumentException('Recipient email cannot be empty.');
        }

        $this->testMailer->send($recipient);

        $symfonyStyle->success(sprintf('Test email sent to %s (check Mailpit UI).', $recipient));

        return Command::SUCCESS;
    }
}
