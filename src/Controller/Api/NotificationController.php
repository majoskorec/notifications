<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Controller\Api;

use App\Notification\Exception\TypeIsNotConfigured;
use App\Notification\Exception\UnknownType;
use App\Notification\Message\Producer;
use App\Notification\Model\Notification;
use App\Notification\Model\Type;
use App\Notification\Model\Version;
use App\Notification\Model\VersionType;
use App\Notification\ModelClassProvider;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class NotificationController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ModelClassProvider $modelClassProvider,
        private Producer $producer
    ) {}

    public function __invoke(string $versionName, string $typeName, Request $request): JsonResponse
    {
        try {
            $type = new Type($typeName);
        } catch (UnknownType $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $version = Version::create($versionName);
        $versionType = VersionType::create($type, $version);

        try {
            $class = $this->modelClassProvider->getModelClass($versionType);
        } catch (TypeIsNotConfigured $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $body = $request->getContent();
        try {
            /** @var Notification $notification */
            $notification = $this->serializer->deserialize($body, $class, 'json');
            // @todo add notification model validation
        } catch (Throwable $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->producer->produce($body, $notification->getOptions(), $versionType);

        return new JsonResponse('OK');
    }
}
