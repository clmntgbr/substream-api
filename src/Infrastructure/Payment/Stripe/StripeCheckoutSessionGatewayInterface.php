<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\Payment\Dto\Preview;
use App\Domain\Plan\Entity\Plan;
use App\Domain\User\Entity\User;
use Stripe\Subscription;

interface StripeCheckoutSessionGatewayInterface
{
    public function create(Plan $plan, User $user): string;

    public function preview(Plan $plan, User $user): Preview;

    public function update(Plan $plan, User $user): Subscription;

    public function retrieve(string $subscriptionId): Subscription;
}
