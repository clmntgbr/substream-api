<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateSubscriptionCommand;
use App\Entity\Subscription;
use App\Enum\SubscriptionStatusEnum;
use App\Exception\PlanNotFoundException;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
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
            throw new PlanNotFoundException($command->getPlanReference());
        }

        $subscription = Subscription::create(
            user: $command->getUser(),
            plan: $plan,
            startDate: new \DateTime(),
            endDate: (new \DateTime())->modify($plan->getExpirationDays()),
            status: SubscriptionStatusEnum::ACTIVE->value,
            autoRenew: true,
        );

        $this->subscriptionRepository->saveAndFlush($subscription);

        return $subscription;
    }
}
