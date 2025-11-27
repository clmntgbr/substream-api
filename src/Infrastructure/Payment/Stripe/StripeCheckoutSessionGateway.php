<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\Payment\Dto\Preview;
use App\Domain\Plan\Entity\Plan;
use App\Domain\User\Entity\User;
use Stripe\Invoice;
use Stripe\StripeClient;
use Stripe\Subscription;

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
            'success_url' => $this->frontendUrl.'/studio/subscription/success',
            'cancel_url' => $this->frontendUrl.'/studio/account',
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

    public function update(Plan $plan, User $user): Subscription
    {
        $subscription = $user->getActiveSubscription();
        $currentPlan = $subscription->getPlan();

        $stripe = new StripeClient($this->stripeApiSecretKey);

        $currentItemId = $this->getCurrentItemId($user);

        $options = [
            'items' => [[
                'id' => $currentItemId,
                'price' => $plan->getStripePriceId(),
            ]],
            'proration_behavior' => 'always_invoice',
            'billing_cycle_anchor' => 'unchanged',
            'cancel_at_period_end' => false,
        ];

        if ($currentPlan->getInterval() !== $plan->getInterval()) {
            $options['billing_cycle_anchor'] = 'now';
        }

        return $stripe->subscriptions->update($subscription->getSubscriptionId(), $options);
    }

    private function getCurrentItemId(User $user): string
    {
        $subscription = $user->getActiveSubscription();
        $stripeSubscription = $this->retrieve($subscription->getSubscriptionId());

        if (null === $stripeSubscription->items->data[0]->id) {
            throw new \RuntimeException('Subscription item ID is required');
        }

        return $stripeSubscription->items->data[0]->id;
    }

    public function preview(Plan $plan, User $user): Preview
    {
        $subscription = $user->getActiveSubscription();
        $currentPlan = $subscription->getPlan();

        $stripe = new StripeClient($this->stripeApiSecretKey);

        $currentItemId = $this->getCurrentItemId($user);

        $options = [
            'customer' => $user->getStripeCustomerId(),
            'subscription' => $subscription->getSubscriptionId(),
            'subscription_details' => [
                'items' => [
                    [
                        'id' => $currentItemId,
                        'price' => $plan->getStripePriceId(),
                    ],
                ],
                'proration_behavior' => 'always_invoice',
                'billing_cycle_anchor' => 'unchanged',
            ],
        ];

        if ($currentPlan->getInterval() !== $plan->getInterval()) {
            $options['subscription_details']['billing_cycle_anchor'] = 'now';
        }

        $preview = $stripe->invoices->createPreview($options);

        return new Preview(
            amountDue: $preview->amount_due / 100,
            credit: $this->getCredit($preview) / 100,
            debit: $this->getDebit($preview) / 100,
            currency: $preview->currency,
            nextBillingDate: date('Y-m-d H:i:s', $preview->period_end),
        );
    }

    private function getCredit(Invoice $invoice): float
    {
        $credit = 0;
        foreach ($invoice->lines->data as $line) {
            if ($line->amount < 0) {
                $credit += abs($line->amount);
            }
        }

        return $credit;
    }

    private function getDebit(Invoice $invoice): float
    {
        $debit = 0;
        foreach ($invoice->lines->data as $line) {
            if ($line->amount > 0) {
                $debit += $line->amount;
            }
        }

        return $debit;
    }

    public function retrieve(string $subscriptionId): Subscription
    {
        $stripe = new StripeClient($this->stripeApiSecretKey);

        /** @var Subscription $subscription */
        $subscription = $stripe->subscriptions->retrieve($subscriptionId);

        return $subscription;
    }
}
