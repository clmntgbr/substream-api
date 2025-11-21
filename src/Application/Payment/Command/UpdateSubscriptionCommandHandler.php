<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Repository\SubscriptionRepository;
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
        $user = $this->userRepository->findOneBy([
            'id' => $command->getUserId(),
        ]);

        if (null === $user) {
            $this->logger->error('User not found', [
                'user_id' => $command->getUserId(),
            ]);

            return;
        }

        $subscription = $user->getActiveSubscription();
        if ($subscription->isFreeSubscription()) {
            throw new \Exception('User has a free subscription, you cannot update it');
        }

        $plan = $this->planRepository->findOneBy(['id' => $command->getPlanId()]);

        if (null === $plan) {
            $this->logger->error('Plan not found', [
                'plan_id' => $command->getPlanId(),
            ]);

            return;
        }

        if ($plan->isFree()) {
            throw new \Exception('You cannot update to a free plan');
        }

        $this->stripeCheckoutSessionGateway->update($plan, $user);

        // TODO update dans un webhook
        $subscription->setEndDate((new DateTime())->modify($plan->getExpirationDays()));
        $subscription->setPlan($plan);

        $this->subscriptionRepository->saveAndFlush($subscription);

        $this->mercurePublisher->refreshPlan($user);
        $this->mercurePublisher->refreshSubscription($user);
    }
}
