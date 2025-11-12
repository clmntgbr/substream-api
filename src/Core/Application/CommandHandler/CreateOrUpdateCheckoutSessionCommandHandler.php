<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateOrUpdateCheckoutSessionCommand;
use App\Enum\PlanTypeEnum;
use App\Service\StripeServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateOrUpdateCheckoutSessionCommandHandler
{
    public function __construct(
        private StripeServiceInterface $stripeService,
    ) {
    }

    public function __invoke(CreateOrUpdateCheckoutSessionCommand $command): string
    {
        $subscription = $command->getSubscription();

        // if ($subscription->getPlan()->getReference() === PlanTypeEnum::FREE->value) {
        return $this->stripeService->createCheckoutSession($command->getPlan(), $command->getUser());
        // }

        // return $this->stripeService->updateCheckoutSession($command->getUser(), $subscription, $command->getPlan());
    }
}
