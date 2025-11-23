<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Application\Subscription\Command\CreateSubscriptionCommand;
use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Payment\Stripe\StripeCheckoutSessionGatewayInterface;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CancelSubscriptionCommandHandler
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

    public function __invoke(CancelSubscriptionCommand $command): void
    {
        $user = $this->userRepository->findOneBy(['stripeCustomerId' => $command->getUserStripeId()]);

        if (null === $user) {
            $this->logger->error('User not found', ['userStripeId' => $command->getUserStripeId()]);

            return;
        }

        $subscription = $this->subscriptionRepository->findOneBy([
            'subscriptionId' => $command->getSubscriptionId(),
            'isActive' => true,
        ]);

        if (null === $subscription) {
            $this->logger->error('Subscription not found', ['subscriptionId' => $command->getSubscriptionId()]);

            return;
        }

        $plan = $this->planRepository->findOneBy(['reference' => 'plan_free']);

        if (null === $plan) {
            $this->logger->error('Plan not found', ['planReference' => 'plan_free']);

            return;
        }

        $subscription->expire();
        $this->subscriptionRepository->saveAndFlush($subscription);

        $this->commandBus->dispatch(new CreateSubscriptionCommand(
            user: $user,
            planReference: $plan->getReference(),
        ));

        // TODO: Send an email to the user to inform them that their subscription has been deleted and plan to free user
        $this->mercurePublisher->refreshPlan($user);
        $this->mercurePublisher->refreshSubscription($user);
    }
}
