<?php

namespace App\Application\Payment\RemoteEvent;

use App\Application\Payment\Command\CreateCheckoutCommand;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Stripe\Event;
use Stripe\StripeObject;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('createcheckout')]
final class CreateCheckoutWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var Event $payload */
        $payload = $event->getPayload()['payload'];

        /** @var StripeObject $stripeObject */
        $stripeObject = $payload->data->object;

        $createCheckoutCommand = new CreateCheckoutCommand(
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
        );

        $this->commandBus->dispatch($createCheckoutCommand);
    }
}
