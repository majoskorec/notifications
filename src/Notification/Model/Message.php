<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Model;

final class Message
{
    public function __construct(private VersionType $versionType, private string $payload) {}

    public function getVersionType(): VersionType
    {
        return $this->versionType;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }
}
