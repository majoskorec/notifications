<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Model;

use JetBrains\PhpStorm\Pure;

final class VersionType
{
    private function __construct(private Type $type, private Version $version) {}

    #[Pure]
    public static function create(Type $type, Version $version): VersionType
    {
        return new self($type, $version);
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    #[Pure]
    public function __toString(): string
    {
        return sprintf('%s|%s', (string)$this->type, (string)$this->version);
    }
}
