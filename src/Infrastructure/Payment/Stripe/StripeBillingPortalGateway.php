<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\User\Entity\User;
use Stripe\StripeClient;

class StripeBillingPortalGateway implements StripeBillingPortalGatewayInterface
{
    public function __construct(
        private readonly string $frontendUrl,
        private readonly string $stripeApiSecretKey,
    ) {
    }

    public function getUrl(User $user): string
    {
        $stripe = new StripeClient($this->stripeApiSecretKey);

        if (null === $user->getStripeCustomerId()) {
            throw new \RuntimeException('User has no Stripe customer ID');
        }

        $billingPortalSession = $stripe->billingPortal->sessions->create([
            'customer' => (string) $user->getStripeCustomerId(),
            'return_url' => $this->frontendUrl.'/studio/billing',
        ]);

        if (null === $billingPortalSession->url) {
            throw new \RuntimeException('Billing portal session URL is required');
        }

        return $billingPortalSession->url;
    }
}
