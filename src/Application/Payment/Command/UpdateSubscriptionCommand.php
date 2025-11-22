<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\AsynchronousPriorityInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateSubscriptionCommand implements AsynchronousPriorityInterface
{
    public function __construct(
        #[Assert\NotBlank]
        private string $userStripeId,
        #[Assert\NotBlank]
        private string $planId,
    ) {
    }

    public function getUserStripeId(): string
    {
        return $this->userStripeId;
    }

    public function getPlanId(): string
    {
        return $this->planId;
    }

    public function jsonSerialize(): array
    {
        return [
            'userStripeId' => $this->userStripeId,
            'planId' => $this->planId,
        ];
    }

    public function getStamps(): array
    {
        return [];
    }
}
