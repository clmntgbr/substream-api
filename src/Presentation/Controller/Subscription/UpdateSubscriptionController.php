<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Subscription;

use App\Domain\Plan\Repository\PlanRepository;
use App\Domain\Subscription\Dto\UpdateSubscriptionPayload;
use App\Domain\User\Entity\User;
use App\Infrastructure\Payment\Stripe\StripeCheckoutSessionGatewayInterface;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

#[AsController]
class UpdateSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly PlanRepository $planRepository,
        private readonly CommandBusInterface $commandBus,
        private readonly StripeCheckoutSessionGatewayInterface $stripeCheckoutSessionGateway,
        private readonly MercurePublisherInterface $mercurePublisher,
    ) {
    }

    public function __invoke(#[MapRequestPayload()] UpdateSubscriptionPayload $upgradeSubscription, #[CurrentUser] User $user): JsonResponse
    {
        if ($user->getActiveSubscription()->isFreeSubscription()) {
            throw new \Exception('User already has a free subscription');
        }

        $plan = $this->planRepository->findByUuid(Uuid::fromString($upgradeSubscription->getPlanId()));

        if (null === $plan) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Plan not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($plan->isFree()) {
            throw new \Exception('You cannot upgrade to a free plan');
        }

        $this->stripeCheckoutSessionGateway->update($plan, $user);

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
