<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Exception;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

final class UnknownType extends InvalidArgumentException
{
    #[Pure]
    private function __construct(string $type)
    {
        parent::__construct(sprintf('unknown notification type `%s`', $type));
    }

    #[Pure]
    public static function create(string $type): UnknownType
    {
        return new self($type);
    }
}
