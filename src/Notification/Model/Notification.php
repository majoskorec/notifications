<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Model;

interface Notification
{
    public static function getVersionType(): VersionType;

    public function getOptions(): NotificationOptions;
}
