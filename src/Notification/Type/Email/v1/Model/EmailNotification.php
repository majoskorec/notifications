<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\Email\v1\Model;

use App\Notification\Model\Notification;
use App\Notification\Model\NotificationOptions;
use App\Notification\Model\Type;
use App\Notification\Model\Version;
use App\Notification\Model\VersionType;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation as Serializer;

final class EmailNotification implements Notification
{
    private const VERSION = 'v1';

    private string $subject;
    private string $to;
    private string $body;
    private string $from;

    /**
     * @Serializer\SerializedName("content_type")
     */
    private string $contentType = 'text/html';

    /**
     * @Serializer\Type("App\Notification\Model\NotificationOptions")
     */
    private NotificationOptions $options;

    #[Pure]
    public function __construct()
    {
        $this->options = new NotificationOptions();
    }

    public static function getVersionType(): VersionType
    {
        return VersionType::create(Type::createEmail(), Version::create(self::VERSION));
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getOptions(): NotificationOptions
    {
        return $this->options;
    }
}
