<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Subscription;

use App\Application\Payment\Command\CreateStripeCheckoutSessionCommand;
use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\User\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

#[AsController]
class CreateSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly PlanRepository $planRepository,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(#[CurrentUser] User $user, string $planId): JsonResponse
    {
        $plan = $this->planRepository->findByUuid(Uuid::fromString($planId));

        if (null === $plan) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Plan not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $checkoutUrl = $this->commandBus->dispatch(new CreateStripeCheckoutSessionCommand(
            user: $user,
            plan: $plan,
            subscription: $user->getActiveSubscription(),
        ));

        return new JsonResponse([
            'success' => true,
            'data' => [
                'url' => $checkoutUrl,
            ],
        ]);
    }
}
