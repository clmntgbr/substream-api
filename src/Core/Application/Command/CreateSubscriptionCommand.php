<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Core\Domain\User\Entity\User;
use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;

final class CreateSubscriptionCommand extends CommandAbstract implements SyncCommandInterface
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
