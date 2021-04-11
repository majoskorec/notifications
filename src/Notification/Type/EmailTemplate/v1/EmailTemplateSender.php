<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\EmailTemplate\v1;

use App\Notification\Exception\WrongMessageForSender;
use App\Notification\Model\Notification;
use App\Notification\Model\Type;
use App\Notification\Model\Version;
use App\Notification\Model\VersionType;
use App\Notification\Sender\Sender;
use App\Notification\Type\EmailTemplate\v1\Adapter\EmailTemplateAdapter;
use App\Notification\Type\EmailTemplate\v1\Model\EmailTemplateNotification;

final class EmailTemplateSender implements Sender
{
    private const VERSION = 'v1';

    public function __construct(private EmailTemplateAdapter $adapter) {}

    public function getVersionType(): VersionType
    {
        return VersionType::create(Type::createEmailTemplate(), Version::create(self::VERSION));
    }

    public function send(Notification $notification): void
    {
        if (!$notification instanceof EmailTemplateNotification) {
            throw WrongMessageForSender::create(get_class($notification), get_class($this), EmailTemplateNotification::class);
        }

        $this->adapter->send($notification);
    }
}
