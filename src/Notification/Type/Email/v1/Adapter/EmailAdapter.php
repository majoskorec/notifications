<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\Email\v1\Adapter;

use App\Notification\Type\Email\v1\Model\EmailNotification;

interface EmailAdapter
{
    public function send(EmailNotification $notification): void;
}
