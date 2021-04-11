<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\EmailTemplate\v1\Adapter;

use App\Notification\Type\EmailTemplate\v1\Model\EmailTemplateNotification;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

final class SwiftMailerGmailTemplateAdapter implements EmailTemplateAdapter
{
    public function __construct(
        private Swift_Mailer $mailer,
        private LoggerInterface $swiftMailerLogger,
        private Environment $twig
    ) {}

    public function send(EmailTemplateNotification $notification): void
    {
        $subject = $this->twig->render(
            'email_template/' . $notification->getSubjectTemplate() . '.html.twig',
            $notification->getSubjectTemplateParams(),
        );

        $body = $this->twig->render(
            'email_template/' . $notification->getBodyTemplate() . '.html.twig',
            $notification->getBodyTemplateParams(),
        );

        $message = (new Swift_Message($subject))
            ->setFrom($notification->getFrom())
            ->setTo($notification->getTo())
            ->setBody($body, $notification->getContentType());

        $this->mailer->send($message);

        $this->swiftMailerLogger->info('mail was send');
    }
}
