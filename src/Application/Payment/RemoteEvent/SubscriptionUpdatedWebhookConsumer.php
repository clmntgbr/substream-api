<?php

namespace App\Application\Payment\RemoteEvent;

use App\Application\Payment\Command\UpdateSubscriptionCommand;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Stripe\StripeObject;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('subscriptionupdated')]
final class SubscriptionUpdatedWebhookConsumer implements ConsumerInterface
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

        $updateSubscriptionCommand = new UpdateSubscriptionCommand(
            userStripeId: $stripeObject->customer,
            planId: $stripeObject->plan->id,
        );

        $this->logger->info('Subscription updated', $updateSubscriptionCommand->jsonSerialize());
        $this->commandBus->dispatch($updateSubscriptionCommand);
    }
}
