<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Type\EmailTemplate\v1\Model;

use App\Notification\Model\Notification;
use App\Notification\Model\NotificationOptions;
use App\Notification\Model\Type;
use App\Notification\Model\Version;
use App\Notification\Model\VersionType;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation as Serializer;

final class EmailTemplateNotification implements Notification
{
    private const VERSION = 'v1';

    /**
     * @Serializer\SerializedName("subject_template")
     */
    private string $subjectTemplate;

    /**
     * @Serializer\SerializedName("body_template")
     */
    private string $bodyTemplate;
    private string $to;
    private string $from;

    /**
     * @var array<string, string>
     * @Serializer\Type("array<string, string>")
     * @Serializer\SerializedName("subject_template_params")
     */
    private array $subjectTemplateParams = [];

    /**
     * @var array<string, string>
     * @Serializer\Type("array<string, string>")
     * @Serializer\SerializedName("body_template_params")
     */
    private array $bodyTemplateParams = [];

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
        return VersionType::create(Type::createEmailTemplate(), Version::create(self::VERSION));
    }

    public function getSubjectTemplate(): string
    {
        return $this->subjectTemplate;
    }

    public function getBodyTemplate(): string
    {
        return $this->bodyTemplate;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return array<string, string>
     */
    public function getSubjectTemplateParams(): array
    {
        return $this->subjectTemplateParams;
    }

    /**
     * @return array<string, string>
     */
    public function getBodyTemplateParams(): array
    {
        return $this->bodyTemplateParams;
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
