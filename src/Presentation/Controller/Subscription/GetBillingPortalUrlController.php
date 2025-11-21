<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Subscription;

use App\Domain\User\Entity\User;
use App\Infrastructure\Payment\Stripe\StripeBillingPortalGatewayInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class GetBillingPortalUrlController extends AbstractController
{
    public function __construct(
        private readonly StripeBillingPortalGatewayInterface $stripeBillingPortalGateway,
    ) {
    }

    public function __invoke(#[CurrentUser] User $user): JsonResponse
    {
        $billingPortalUrl = $this->stripeBillingPortalGateway->getUrl($user);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'url' => $billingPortalUrl,
            ],
        ]);
    }
}
