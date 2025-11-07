<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateSubscriptionCommand;
use App\Entity\Subscription;
use App\Enum\SubscriptionStatusEnum;
use App\Exception\PlanNotFoundException;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[AsMessageHandler]
class CreateSubscriptionCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PlanRepository $planRepository,
        private SubscriptionRepository $subscriptionRepository,
    ) {
    }

    public function __invoke(CreateSubscriptionCommand $command): Subscription
    {
        $user = $this->userRepository->findByUuid($command->getUserId());

        if (null === $user) {
            throw new UserNotFoundException((string) $command->getUserId());
        }

        $plan = $this->planRepository->findByUuid($command->getPlanId());

        if (null === $plan) {
            throw new PlanNotFoundException((string) $command->getPlanId());
        }

        $subscription = Subscription::create(
            user: $user,
            plan: $plan,
            startDate: new \DateTime(),
            status: SubscriptionStatusEnum::ACTIVE->value,
            autoRenew: true,
        );

        $this->subscriptionRepository->saveAndFlush($subscription);

        return $subscription;
    }
}
