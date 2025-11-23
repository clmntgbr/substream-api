<?php

namespace App\Application\Payment\RemoteEvent;

use App\Application\Payment\Command\DeleteSubscriptionCommand;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Stripe\StripeObject;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('subscriptiondeleted')]
final class SubscriptionDeletedWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var Event $payload */
        $payload = $event->getPayload()['payload'];

        /** @var StripeObject $stripeObject */
        $stripeObject = $payload->data->object;

        $deleteSubscriptionCommand = new DeleteSubscriptionCommand(
            userStripeId: $stripeObject->customer,
            subscriptionId: $stripeObject->id,
        );

        $this->commandBus->dispatch($deleteSubscriptionCommand);
    }
}
