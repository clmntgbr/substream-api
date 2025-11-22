<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\AsynchronousPriorityInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class DeleteSubscriptionCommand implements AsynchronousPriorityInterface
{
    public function __construct(
        #[Assert\NotBlank]
        private string $userStripeId,
        #[Assert\NotBlank]
        private string $subscriptionId,
    ) {
    }

    public function getUserStripeId(): string
    {
        return $this->userStripeId;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function jsonSerialize(): array
    {
        return [
            'userStripeId' => $this->userStripeId,
            'subscriptionId' => $this->subscriptionId,
        ];
    }

    public function getStamps(): array
    {
        return [];
    }
}
