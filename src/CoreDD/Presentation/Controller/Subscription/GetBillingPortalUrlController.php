<?php

declare(strict_types=1);

namespace App\CoreDD\Presentation\Controller\Subscription;

use App\CoreDD\Domain\User\Entity\User;
use App\CoreDD\Infrastructure\Payment\Stripe\StripeSubscriptionGatewayInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class GetBillingPortalUrlController extends AbstractController
{
    public function __construct(
        private readonly StripeSubscriptionGatewayInterface $stripeSubscriptionGateway,
    ) {
    }

    public function __invoke(#[CurrentUser] User $user): JsonResponse
    {
        $billingPortalUrl = $this->stripeSubscriptionGateway->getBillingPortalUrl($user);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'url' => $billingPortalUrl,
            ],
        ]);
    }
}
