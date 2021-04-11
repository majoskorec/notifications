<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Exception;

use App\Notification\Model\Notification;
use JetBrains\PhpStorm\Pure;
use LogicException;

final class WrongNotificationModelClass extends LogicException
{
    #[Pure]
    private function __construct(string $class)
    {
        parent::__construct(sprintf(
            'notification model class `%s` does not implement `%s`',
            $class,
            Notification::class,
        ));
    }

    #[Pure]
    public static function create(string $class): WrongNotificationModelClass
    {
        return new self($class);
    }
}
