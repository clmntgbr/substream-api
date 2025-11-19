<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Domain\Payment\Entity\Payment;
use App\Domain\Payment\Repository\PaymentRepository;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePaymentCommandHandler
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private PaymentRepository $paymentRepository,
    ) {
    }

    public function __invoke(CreatePaymentCommand $command): void
    {
        $subscription = $this->subscriptionRepository->findOneBy(['subscriptionId' => $command->getSubscriptionId()]);

        if (null === $subscription) {
            throw new \Exception('Subscription not found');
        }

        $payment = Payment::create(
            subscription: $subscription,
            customerId: $command->getCustomerId(),
            invoiceId: $command->getInvoiceId(),
            paymentStatus: $command->getPaymentStatus(),
            currency: $command->getCurrency(),
            amount: $command->getAmount(),
        );

        $this->paymentRepository->saveAndFlush($payment);
    }
}
