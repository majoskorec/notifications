<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\Email\v1;

use App\Notification\Exception\WrongMessageForSender;
use App\Notification\Model\Notification;
use App\Notification\Model\Type;
use App\Notification\Model\Version;
use App\Notification\Model\VersionType;
use App\Notification\Sender\Sender;
use App\Notification\Type\Email\v1\Adapter\EmailAdapter;
use App\Notification\Type\Email\v1\Model\EmailNotification;

final class EmailSender implements Sender
{
    private const VERSION = 'v1';

    public function __construct(private EmailAdapter $adapter) {}

    public function getVersionType(): VersionType
    {
        return VersionType::create(Type::createEmail(), Version::create(self::VERSION));
    }

    public function send(Notification $notification): void
    {
        if (!$notification instanceof EmailNotification) {
            throw WrongMessageForSender::create(get_class($notification), get_class($this), EmailNotification::class);
        }

        $this->adapter->send($notification);
    }
}
