<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStripePaymentCommand;
use App\Entity\StripePayment;
use App\Exception\SubscriptionNotFoundException;
use App\Repository\PlanRepository;
use App\Repository\StripePaymentRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStripePaymentCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PlanRepository $planRepository,
        private SubscriptionRepository $subscriptionRepository,
        private StripePaymentRepository $stripePaymentRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateStripePaymentCommand $command): void
    {
        $subscription = $this->subscriptionRepository->findOneBy(['subscriptionId' => $command->getSubscriptionId()]);

        if (null === $subscription) {
            throw new SubscriptionNotFoundException($command->getSubscriptionId());
        }

        $stripePayment = StripePayment::create(
            subscription: $subscription,
            checkoutSessionId: $command->getCheckoutSessionId(),
            customerId: $command->getCustomerId(),
            invoiceId: $command->getInvoiceId(),
            paymentStatus: $command->getPaymentStatus(),
            currency: $command->getCurrency(),
            amount: $command->getAmount(),
        );

        $this->stripePaymentRepository->saveAndFlush($stripePayment);
    }
}
