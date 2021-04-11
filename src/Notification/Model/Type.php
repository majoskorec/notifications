<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Model;

use App\Notification\Exception\UnknownType;

final class Type
{
    private const EMAIL = 'email';
    private const EMAIL_TEMPLATE = 'email_template';
    private const SMS = 'sms';
    private const PUSH = 'push_notification';

    private static array $allowedTypes = [
        self::EMAIL,
        self::EMAIL_TEMPLATE,
        self::SMS,
        self::PUSH,
    ];

    public function __construct(private string $type)
    {
        if (!in_array($type, self::$allowedTypes, true)) {
            throw UnknownType::create($type);
        }
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public static function createEmail(): Type
    {
        return new self(self::EMAIL);
    }

    public static function createEmailTemplate(): Type
    {
        return new self(self::EMAIL_TEMPLATE);
    }

    public static function createSms(): Type
    {
        return new self(self::SMS);
    }

    public static function createPushNotification(): Type
    {
        return new self(self::PUSH);
    }
}
