<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\Email\v1\Adapter;

use App\Notification\Type\Email\v1\Model\EmailNotification;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;

final class SwiftMailerGmailAdapter implements EmailAdapter
{
    public function __construct(private Swift_Mailer $mailer, private LoggerInterface $swiftMailerLogger) {}

    public function send(EmailNotification $notification): void
    {
        $message = (new Swift_Message($notification->getSubject()))
            ->setFrom($notification->getFrom())
            ->setTo($notification->getTo())
            ->setBody($notification->getBody(), $notification->getContentType());

        $this->mailer->send($message);

        $this->swiftMailerLogger->info('mail was send');
    }
}
