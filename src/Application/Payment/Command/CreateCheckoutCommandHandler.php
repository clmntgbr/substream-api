<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Application\Subscription\Command\CreateSubscriptionCommand;
use App\Domain\Plan\Entity\Plan;
use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateCheckoutCommandHandler
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

    public function __invoke(CreateCheckoutCommand $command): void
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

        $this->expireCurrentSubscription($user);
        $this->createNewSubscription($user, $plan, $command->getCheckoutSessionId(), $command->getSubscriptionId());

        $user->setStripeCustomerId($command->getStripeCustomerId());
        $this->userRepository->saveAndFlush($user);

        $this->mercurePublisher->refreshPlan($user);
        $this->mercurePublisher->refreshSubscription($user);
    }

    private function expireCurrentSubscription(User $user): void
    {
        $subscription = $user->getActiveSubscription();
        $subscription->expire();

        $this->subscriptionRepository->saveAndFlush($subscription);
    }

    private function createNewSubscription(User $user, Plan $plan, string $checkoutSessionId, string $subscriptionId): void
    {
        $subscription = $this->commandBus->dispatch(new CreateSubscriptionCommand(
            user: $user,
            planReference: $plan->getReference(),
            checkoutSessionId: $checkoutSessionId,
            subscriptionId: $subscriptionId,
        ));

        $this->subscriptionRepository->saveAndFlush($subscription);
    }
}
