<?php

declare(strict_types=1);

namespace App\Application\Subscription\Command;

use App\Domain\User\Entity\User;
use App\Shared\Application\Command\SynchronousInterface;

final class CreateSubscriptionCommand implements SynchronousInterface
{
    public function __construct(
        private User $user,
        private string $planReference,
        private ?string $subscriptionId = null,
        private ?string $checkoutSessionId = null,
    ) {
    }

    public function getCheckoutSessionId(): ?string
    {
        return $this->checkoutSessionId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPlanReference(): string
    {
        return $this->planReference;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }
}
