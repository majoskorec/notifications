<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification;

use App\Notification\Exception\MissingSender;
use App\Notification\Model\VersionType;
use App\Notification\Sender\Sender;
use JetBrains\PhpStorm\Pure;

final class SenderProvider
{
    /**
     * @var array<string, Sender>
     */
    private array $senders;

    /**
     * @param iterable<Sender> $senders
     */
    public function __construct(ModelClassProvider $modelClassProvider, iterable $senders)
    {

        $this->senders = [];
        foreach ($senders as $sender) {
            if (!$modelClassProvider->hasModelClass($sender->getVersionType())) {
                continue;
            }

            $this->senders[(string)$sender->getVersionType()] = $sender;
        }

        if (count($modelClassProvider->getModelClasses()) === count($this->senders)) {
            return;
        }

        foreach (array_keys($modelClassProvider->getModelClasses()) as $versionTypeName) {
            if (array_key_exists($versionTypeName, $this->senders)) {
                continue;
            }

            throw MissingSender::createFromVersionTypeName($versionTypeName);
        }
    }

    #[Pure]
    public function hasSender(VersionType $type): bool
    {
        return array_key_exists((string)$type, $this->senders);
    }

    public function getSender(VersionType $type): Sender
    {
        if (!$this->hasSender($type)) {
            throw MissingSender::createFromVersionType($type);
        }

        return $this->senders[(string)$type];
    }
}
