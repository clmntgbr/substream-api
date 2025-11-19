<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\Plan\Entity\Plan;
use App\Domain\User\Entity\User;
use Stripe\StripeClient;

class StripeCheckoutSessionGateway implements StripeCheckoutSessionGatewayInterface
{
    public function __construct(
        private readonly string $frontendUrl,
        private readonly string $stripeApiSecretKey,
    ) {
    }

    public function create(Plan $plan, User $user): string
    {
        $stripe = new StripeClient($this->stripeApiSecretKey);

        $session = $stripe->checkout->sessions->create([
            'client_reference_id' => (string) $user->getId(),
            'customer_email' => $user->getEmail(),
            'success_url' => $this->frontendUrl.'/subscribe/success',
            'cancel_url' => $this->frontendUrl.'/pricing',
            'line_items' => [[
                'price' => $plan->getStripePriceId(),
                'quantity' => 1,
            ]],
            'currency' => 'eur',
            'mode' => 'subscription',
            'metadata' => [
                'plan_id' => (string) $plan->getId(),
                'plan_name' => $plan->getName(),
                'plan_price' => $plan->getPrice(),
                'currency' => 'eur',
                'plan_interval' => $plan->getInterval(),
                'user_id' => (string) $user->getId(),
                'user_email' => $user->getEmail(),
                'user_name' => $user->getName(),
            ],
        ]);

        if (null === $session->url) {
            throw new \RuntimeException('Checkout session URL is required');
        }

        return $session->url;
    }
}
