<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Subscription;

use App\Application\Payment\Command\CreateStripeCheckoutSessionCommand;
use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Dto\CreateSubscriptionPayload;
use App\Domain\User\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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

    public function __invoke(#[MapRequestPayload()] CreateSubscriptionPayload $createSubscription, #[CurrentUser] User $user): JsonResponse
    {
        if ($user->getActiveSubscription()->isPaidSubscription()) {
            throw new Exception('User already has a paid subscription');
        }

        $plan = $this->planRepository->findByUuid(Uuid::fromString($createSubscription->getPlanId()));

        if (null === $plan) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Plan not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($plan->isFree()) {
            throw new Exception('You cannot create a subscription for a free plan');
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
