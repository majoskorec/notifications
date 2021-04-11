<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Exception;

use App\Notification\Model\VersionType;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

final class TypeIsNotConfigured extends InvalidArgumentException
{
    #[Pure]
    private function __construct(VersionType $versionType)
    {
        parent::__construct(sprintf(
            'notification type `%s` version `%s is not configured',
            (string)$versionType->getType(),
            (string)$versionType->getVersion(),
        ));
    }

    #[Pure]
    public static function create(VersionType $versionType): TypeIsNotConfigured
    {
        return new self($versionType);
    }
}
