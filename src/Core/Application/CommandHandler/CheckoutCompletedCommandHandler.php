<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CheckoutCompletedCommand;
use App\Core\Application\Command\CreateStripePaymentCommand;
use App\Core\Application\Command\CreateSubscriptionCommand;
use App\Enum\SubscriptionStatusEnum;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use App\Service\PublishServiceInterface;
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
        private PublishServiceInterface $publishService,
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
            subscriptionId: $command->getSubscriptionId(),
        ));

        $this->subscriptionRepository->saveAndFlush($subscription);
        $this->subscriptionRepository->saveAndFlush($newSubscription);

        $this->commandBus->dispatch(new CreateStripePaymentCommand(
            checkoutSessionId: $command->getCheckoutSessionId(),
            customerId: $command->getStripeCustomerId(),
            subscriptionId: $command->getSubscriptionId(),
            invoiceId: $command->getStripeInvoiceId(),
            paymentStatus: $command->getPaymentStatus(),
            currency: $command->getCurrency(),
            amount: $command->getAmount(),
        ));

        $this->publishService->refreshPlan($user);
    }
}
