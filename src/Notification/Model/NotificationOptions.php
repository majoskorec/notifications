<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Model;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

final class NotificationOptions
{
    /**
     * @Serializer\SerializedName("response_webhook")
     */
    private ?string $responseWebhook = null;

    /**
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s O'>")
     */
    private ?DateTimeImmutable $sendTime = null;

    public function getResponseWebhook(): ?string
    {
        return $this->responseWebhook;
    }

    public function getSendTime(): ?DateTimeImmutable
    {
        return $this->sendTime;
    }
}
