<?php

namespace App\Application\Payment\RemoteEvent;

use App\Application\Payment\Command\CreatePaymentCommand;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Stripe\StripeObject;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('invoicefailed')]
final class InvoiceFailedWebhookConsumer implements ConsumerInterface
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

        $this->logger->info(json_encode($payload, JSON_PRETTY_PRINT));

        /** @var StripeObject $stripeObject */
        $stripeObject = $payload->data->object;

        $this->logger->info(json_encode($stripeObject, JSON_PRETTY_PRINT));

        $createPaymentCommand = new CreatePaymentCommand(
            customerId: $stripeObject->customer,
            subscriptionId: $stripeObject->parent->subscription_details->subscription,
            amount: $stripeObject->amount_paid,
            currency: $stripeObject->currency,
            invoiceId: $stripeObject->id,
            paymentStatus: $stripeObject->status,
        );

        $this->commandBus->dispatch($createPaymentCommand);
    }
}
