<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Message;

use App\Notification\Model\Message;
use App\Notification\Model\NotificationOptions;
use App\Notification\Model\VersionType;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class Producer
{
    public function __construct(private MessageBusInterface $bus) {}

    public function produce(string $payload, NotificationOptions $options, VersionType $versionType): void
    {
        $message = new Message($versionType, $payload);

        $sendTime = $options->getSendTime();
        if ($sendTime !== null) {
            $message = new Envelope($message, [$this->createDelayStamp($sendTime)]);
        }

        $this->bus->dispatch($message);
    }

    private function createDelayStamp(DateTimeImmutable $sendTime): DelayStamp
    {
        $now = new DateTime('now');
        $delay = $sendTime->getTimestamp() - $now->getTimestamp();
        $delay = max($delay, 0);

        return new DelayStamp($delay * 1000);
    }
}
