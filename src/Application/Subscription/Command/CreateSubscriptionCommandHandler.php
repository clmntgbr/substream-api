<?php

declare(strict_types=1);

namespace App\Application\Subscription\Command;

use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Entity\Subscription;
use App\Domain\Subscription\Enum\SubscriptionStatusEnum;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use Safe\DateTime;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateSubscriptionCommandHandler
{
    public function __construct(
        private PlanRepository $planRepository,
        private SubscriptionRepository $subscriptionRepository,
    ) {
    }

    public function __invoke(CreateSubscriptionCommand $command): Subscription
    {
        $plan = $this->planRepository->findOneBy(['reference' => $command->getPlanReference()]);

        if (null === $plan) {
            throw new \RuntimeException(sprintf('Plan not found: %s', $command->getPlanReference()));
        }

        $subscription = Subscription::create(
            user: $command->getUser(),
            plan: $plan,
            startDate: new DateTime(),
            endDate: (new DateTime())->modify($plan->getExpirationDays()),
            status: SubscriptionStatusEnum::ACTIVE->value,
            autoRenew: true,
            subscriptionId: $command->getSubscriptionId(),
            checkoutSessionId: $command->getCheckoutSessionId(),
        );

        $this->subscriptionRepository->saveAndFlush($subscription);

        return $subscription;
    }
}
