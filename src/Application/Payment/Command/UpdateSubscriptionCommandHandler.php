<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Entity\Subscription;
use App\Domain\Subscription\Enum\SubscriptionStatusEnum;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Payment\Stripe\StripeCheckoutSessionGatewayInterface;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Safe\DateTime;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateSubscriptionCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PlanRepository $planRepository,
        private SubscriptionRepository $subscriptionRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
        private MercurePublisherInterface $mercurePublisher,
        private StripeCheckoutSessionGatewayInterface $stripeCheckoutSessionGateway,
    ) {
    }

    public function __invoke(UpdateSubscriptionCommand $command): void
    {
        $user = $this->userRepository->findOneBy(['stripeCustomerId' => $command->getUserStripeId()]);

        if (null === $user) {
            $this->logger->error('User not found', ['userStripeId' => $command->getUserStripeId()]);

            return;
        }

        $plan = $this->planRepository->findOneBy(['stripePriceId' => $command->getPlanId()]);

        if (null === $plan) {
            $this->logger->error('Plan not found', ['planId' => $command->getPlanId()]);

            return;
        }

        $subscription = $user->getActiveSubscription();

        $subscription->setPlan($plan);

        if (null !== $command->getCancelAt()) {
            $this->cancelSubscription($subscription, $command->getCancelAt());
            $this->refreshSubscription($user);

            return;
        }

        $this->uncancelSubscription($subscription);
        $this->refreshSubscription($user);
    }

    private function cancelSubscription(Subscription $subscription, int $cancelAt): void
    {
        $subscription->setStatus(SubscriptionStatusEnum::PENDING_CANCEL->value);
        $subscription->setEndDate((new DateTime())->setTimestamp($cancelAt));
        $subscription->setAutoRenew(false);
        $this->subscriptionRepository->saveAndFlush($subscription);
    }

    private function uncancelSubscription(Subscription $subscription): void
    {
        $subscription->setStatus(SubscriptionStatusEnum::ACTIVE->value);
        $subscription->setEndDate(null);
        $subscription->setAutoRenew(true);
        $this->subscriptionRepository->saveAndFlush($subscription);
    }

    private function refreshSubscription(User $user): void
    {
        $this->mercurePublisher->refreshPlan($user);
        $this->mercurePublisher->refreshSubscription($user);
    }
}
