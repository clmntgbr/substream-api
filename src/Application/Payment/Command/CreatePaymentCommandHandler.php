<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Domain\Payment\Entity\Payment;
use App\Domain\Payment\Repository\PaymentRepository;
use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Enum\SubscriptionStatusEnum;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePaymentCommandHandler
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private PaymentRepository $paymentRepository,
        private PlanRepository $planRepository,
        private LoggerInterface $logger,
        private MercurePublisherInterface $mercurePublisher,
    ) {
    }

    public function __invoke(CreatePaymentCommand $command): void
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

        $plan = $this->planRepository->findOneBy(['stripePriceId' => $command->getStripePriceId()]);

        if (null === $plan) {
            $this->logger->error('Plan not found', [
                'stripePriceId' => $command->getStripePriceId(),
            ]);

            return;
        }

        $payment = Payment::createFromStripe(
            subscription: $subscription,
            customerId: $command->getCustomerId(),
            invoiceId: $command->getInvoiceId(),
            invoiceUrl: $command->getInvoiceUrl(),
            paymentStatus: $command->getPaymentStatus(),
            currency: $command->getCurrency(),
            amount: $command->getAmount(),
            plan: $plan,
        );

        $subscription->setStatus(SubscriptionStatusEnum::ACTIVE->value);

        $this->subscriptionRepository->saveAndFlush($subscription);
        $this->paymentRepository->saveAndFlush($payment);

        $this->mercurePublisher->refreshPayments($subscription->getUser());
    }
}
