<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Message;

use App\Notification\Model\Message;
use App\Notification\Model\Notification;
use App\Notification\ModelClassProvider;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FailedConsumer implements MessageHandlerInterface
{
    public function __construct(
        private ModelClassProvider $modelClassProvider,
        private SerializerInterface $serializer,
        private LoggerInterface $consumerLogger,
    ) {}

    public function __invoke(Message $message): void
    {
        $model = $this->modelClassProvider->getModelClass($message->getVersionType());
        /** @var Notification $notification */
        $notification = $this->serializer->deserialize($message->getPayload(), $model, 'json');

        $webhook = $notification->getOptions()->getResponseWebhook();
        if ($webhook === null) {
            return;
        }

        $this->sendErrorResponseToWebhook($webhook);
    }

    private function sendErrorResponseToWebhook(string $webhook): void
    {
        $this->consumerLogger->info('sendErrorResponseToWebhook', ['webhook' => $webhook]);
        // @todo idealne vytvorit novu msg do ineho transportu na poslanie webhooku
    }
}
