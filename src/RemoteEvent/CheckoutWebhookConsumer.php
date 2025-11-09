<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\CheckoutCompletedCommand;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Stripe\Event;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('checkout')]
final class CheckoutWebhookConsumer implements ConsumerInterface
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

        $this->commandBus->dispatch(new CheckoutCompletedCommand(
            checkoutSessionId: $payload->data->object->id,
            userId: $payload->data->object->client_reference_id,
            userEmail: $payload->data->object->customer_email,
            planId: $payload->data->object->metadata->plan_id,
            stripeCustomerId: $payload->data->object->customer,
            subscriptionId: $payload->data->object->subscription,
            stripeInvoiceId: $payload->data->object->invoice,
            paymentStatus: $payload->data->object->payment_status,
            amount: $payload->data->object->amount_total,
            currency: $payload->data->object->currency,
        ));
    }
}
