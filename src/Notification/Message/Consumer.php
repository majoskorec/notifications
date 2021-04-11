<?php

declare(strict_types=1);

/*
 * @author mskorupa
 */

namespace App\Notification\Message;

use App\Notification\Model\Message;
use App\Notification\Model\Notification;
use App\Notification\ModelClassProvider;
use App\Notification\SenderProvider;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class Consumer implements MessageHandlerInterface
{
    public function __construct(
        private ModelClassProvider $modelClassProvider,
        private SenderProvider $senderProvider,
        private SerializerInterface $serializer,
        private LoggerInterface $consumerLogger,
    ) {}

    public function __invoke(Message $message): void
    {
        $model = $this->modelClassProvider->getModelClass($message->getVersionType());
        $sender = $this->senderProvider->getSender($message->getVersionType());
        /** @var Notification $notification */
        $notification = $this->serializer->deserialize($message->getPayload(), $model, 'json');

        $sender->send($notification);

        $webhook = $notification->getOptions()->getResponseWebhook();
        if ($webhook === null) {
            return;
        }

        $this->sendSuccessResponseToWebhook($webhook);
    }

    private function sendSuccessResponseToWebhook(string $webhook): void
    {
        $this->consumerLogger->info('sendSuccessResponseToWebhook', ['webhook' => $webhook]);
        // @todo idealne vytvorit novu msg do ineho transportu na poslanie webhooku
    }
}
