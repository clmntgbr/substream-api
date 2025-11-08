<?php

declare(strict_types=1);

namespace App\Controller\Subscription;

use App\Entity\User;
use App\Repository\PlanRepository;
use App\Service\StripeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

#[AsController]
class SubscribeController extends AbstractController
{
    public function __construct(
        private readonly StripeServiceInterface $stripeService,
        private readonly PlanRepository $planRepository,
    ) {
    }

    public function __invoke(#[CurrentUser] User $user, string $planId): JsonResponse
    {
        $plan = $this->planRepository->findByUuid(Uuid::fromString($planId));

        if (!$plan) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Plan not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $checkoutUrl = $this->stripeService->checkoutSession($plan, $user);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'url' => $checkoutUrl,
            ],
        ]);
    }
}
