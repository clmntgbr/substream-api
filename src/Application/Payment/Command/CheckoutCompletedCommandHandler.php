<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Application\Subscription\Command\CreateSubscriptionCommand;
use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Enum\SubscriptionStatusEnum;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CheckoutCompletedCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PlanRepository $planRepository,
        private SubscriptionRepository $subscriptionRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
        private MercurePublisherInterface $mercurePublisher,
    ) {
    }

    public function __invoke(CheckoutCompletedCommand $command): void
    {
        $user = $this->userRepository->findOneBy([
            'id' => $command->getUserId(),
            'email' => $command->getUserEmail(),
        ]);

        if (null === $user) {
            $this->logger->error('User not found', [
                'user_id' => $command->getUserId(),
                'user_email' => $command->getUserEmail(),
            ]);

            return;
        }

        $plan = $this->planRepository->findOneBy(['id' => $command->getPlanId()]);

        if (null === $plan) {
            $this->logger->error('Plan not found', [
                'plan_id' => $command->getPlanId(),
            ]);

            return;
        }

        $subscription = $user->getActiveSubscription();
        $subscription->setStatus(SubscriptionStatusEnum::EXPIRED->value);
        $subscription->setEndDate(new \DateTime('now'));

        $newSubscription = $this->commandBus->dispatch(new CreateSubscriptionCommand(
            user: $user,
            planReference: $plan->getReference(),
            checkoutSessionId: $command->getCheckoutSessionId(),
            subscriptionId: $command->getSubscriptionId(),
        ));

        $user->setStripeCustomerId($command->getStripeCustomerId());
        $this->userRepository->saveAndFlush($user);

        $this->subscriptionRepository->saveAndFlush($subscription);
        $this->subscriptionRepository->saveAndFlush($newSubscription);

        $this->commandBus->dispatch(new CreatePaymentCommand(
            customerId: $command->getStripeCustomerId(),
            subscriptionId: $command->getSubscriptionId(),
            invoiceId: $command->getStripeInvoiceId(),
            paymentStatus: $command->getPaymentStatus(),
            currency: $command->getCurrency(),
            amount: $command->getAmount(),
        ));

        $this->mercurePublisher->refreshPlans($user);
    }
}
