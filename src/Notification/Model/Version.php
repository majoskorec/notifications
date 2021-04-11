<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Model;

use JetBrains\PhpStorm\Pure;

final class Version
{
    private function __construct(private string $version) {}

    #[Pure]
    public static function create(string $version): Version
    {
        return new self($version);
    }

    public function __toString(): string
    {
        return $this->version;
    }
}
