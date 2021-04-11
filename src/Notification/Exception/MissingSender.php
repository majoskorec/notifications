<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Exception;

use App\Notification\Model\VersionType;
use JetBrains\PhpStorm\Pure;
use LogicException;

final class MissingSender extends LogicException
{
    #[Pure]
    private function __construct(string $version, string $type)
    {
        parent::__construct(sprintf(
            'missing sender for type `%s` version `%s`',
            $type,
            $version,
        ));
    }

    public static function createFromVersionTypeName(string $versionTypeName): MissingSender
    {
        return new self('', $versionTypeName);
    }

    #[Pure]
    public static function createFromVersionType(VersionType $versionType): MissingSender
    {
        return new self((string)$versionType->getVersion(), (string)$versionType->getType());
    }
}
