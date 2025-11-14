<?php

namespace App\Application\Payment\RemoteEvent;

use App\Application\Payment\Command\CheckoutCompletedCommand;
use App\Shared\Application\Bus\CommandBusInterface;
use Stripe\Event;
use Stripe\StripeObject;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('checkout')]
final class CheckoutWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var Event $payload */
        $payload = $event->getPayload()['payload'];

        /** @var StripeObject $stripeObject */
        $stripeObject = $payload->data->object;

        $this->commandBus->dispatch(new CheckoutCompletedCommand(
            checkoutSessionId: $stripeObject->id,
            userId: $stripeObject->client_reference_id,
            userEmail: $stripeObject->customer_email,
            planId: $stripeObject->metadata->plan_id,
            stripeCustomerId: $stripeObject->customer,
            subscriptionId: $stripeObject->subscription,
            stripeInvoiceId: $stripeObject->invoice,
            paymentStatus: $stripeObject->payment_status,
            amount: $stripeObject->amount_total,
            currency: $stripeObject->currency,
        ));
    }
}
