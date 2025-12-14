<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class TestMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire('%env(string:MAIL_FROM_ADDRESS)%')]
        private readonly string $fromAddress,
    ) {
    }

    public function send(string $toAddress): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->fromAddress, 'Symfony API Starter'))
            ->to($toAddress)
            ->subject('Test email from Symfony API Starter')
            ->htmlTemplate('email/test_email.html.twig')
            ->context([
                'toAddress' => $toAddress,
            ]);

        $this->mailer->send($templatedEmail);
    }
}
