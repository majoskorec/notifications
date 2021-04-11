<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Sender;

use App\Notification\Model\Notification;
use App\Notification\Model\VersionType;

interface Sender
{
    public function getVersionType(): VersionType;

    public function send(Notification $notification): void;
}
