<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Exception;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

final class WrongMessageForSender extends RuntimeException
{
    #[Pure]
    private function __construct(string $messageClass, string $senderClass, string $expectedMessageClass)
    {
        parent::__construct(sprintf(
            'sender `%s` expect message `%s`, got `%s`',
            $senderClass,
            $expectedMessageClass,
            $messageClass,
        ));
    }

    #[Pure]
    public static function create(
        string $messageClass,
        string $senderClass,
        string $expectedMessageClass
    ): WrongMessageForSender {
        return new self($messageClass, $senderClass, $expectedMessageClass);
    }
}
