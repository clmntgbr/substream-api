<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreatePaymentCommand;
use App\Core\Domain\Payment\Entity\Payment;
use App\Exception\SubscriptionNotFoundException;
use App\Core\Domain\Payment\Repository\PaymentRepository;
use App\Core\Domain\Subscription\Repository\SubscriptionRepository;
use App\Core\Domain\Plan\Repository\PlanRepository;
use App\Core\Domain\User\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePaymentCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PlanRepository $planRepository,
        private SubscriptionRepository $subscriptionRepository,
        private PaymentRepository $paymentRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreatePaymentCommand $command): void
    {
        $subscription = $this->subscriptionRepository->findOneBy(['subscriptionId' => $command->getSubscriptionId()]);

        if (null === $subscription) {
            throw new SubscriptionNotFoundException($command->getSubscriptionId());
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
