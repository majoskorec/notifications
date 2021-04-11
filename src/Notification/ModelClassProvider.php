<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification;

use App\Notification\Exception\TypeIsNotConfigured;
use App\Notification\Exception\WrongNotificationModelClass;
use App\Notification\Model\Notification;
use App\Notification\Model\VersionType;
use JetBrains\PhpStorm\Pure;

final class ModelClassProvider
{
    /**
     * @var array<string, class-string>
     */
    private array $models;

    /**
     * @param array<string> $notifications
     */
    public function __construct(array $notifications)
    {
        $this->models = [];
        foreach ($notifications as $notification) {
            if (!is_subclass_of($notification, Notification::class)) {
                throw WrongNotificationModelClass::create($notification);
            }

            $versionType = $notification::getVersionType();
            $this->models[(string)$versionType] = $notification;
        }
    }

    #[Pure]
    public function hasModelClass(VersionType $versionType): bool
    {
        return array_key_exists((string)$versionType, $this->models);
    }

    /**
     * @return class-string
     */
    public function getModelClass(VersionType $versionType): string
    {
        if (!$this->hasModelClass($versionType)) {
            throw TypeIsNotConfigured::create($versionType);
        }

        return $this->models[(string)$versionType];
    }

    /**
     * @return array<string, class-string>
     */
    public function getModelClasses(): array
    {
        return $this->models;
    }
}
