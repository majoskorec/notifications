<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\EmailTemplate\v1\Adapter;

use App\Notification\Type\EmailTemplate\v1\Model\EmailTemplateNotification;

interface EmailTemplateAdapter
{
    public function send(EmailTemplateNotification $notification): void;
}
