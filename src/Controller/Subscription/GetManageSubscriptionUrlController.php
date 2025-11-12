<?php

declare(strict_types=1);

namespace App\Controller\Subscription;

use App\Core\Domain\User\Entity\User;
use App\Service\StripeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class GetManageSubscriptionUrlController extends AbstractController
{
    public function __construct(
        private readonly StripeServiceInterface $stripeService,
    ) {
    }

    public function __invoke(#[CurrentUser] User $user): JsonResponse
    {
        $manageSubscriptionUrl = $this->stripeService->getManageSubscriptionUrl($user);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'url' => $manageSubscriptionUrl,
            ],
        ]);
    }
}
