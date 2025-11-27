<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Domain\Payment\Repository\PaymentRepository;
use App\Domain\Subscription\Enum\SubscriptionStatusEnum;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePaymentFailedCommandHandler
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private PaymentRepository $paymentRepository,
    ) {
    }

    public function __invoke(CreatePaymentFailedCommand $command): void
    {
        $subscription = $this->subscriptionRepository->findOneBy(['subscriptionId' => $command->getSubscriptionId()]);

        if (null === $subscription) {
            throw new Exception('Subscription not found');
        }

        $existingPayment = $this->paymentRepository->findOneBy([
            'invoiceId' => $command->getInvoiceId(),
        ]);

        if ($existingPayment) {
            return;
        }

        $subscription->setStatus(SubscriptionStatusEnum::PAST_DUE->value);
        $this->subscriptionRepository->saveAndFlush($subscription);

        // TODO: Send an email to the user to inform them that their subscription has failed
    }
}
