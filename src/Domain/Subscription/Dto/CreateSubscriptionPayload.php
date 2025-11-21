<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateSubscriptionPayload
{
    public function __construct(
        #[Assert\Uuid()]
        #[Assert\NotBlank()]
        public string $planId,
    ) {
    }

    public function getPlanId(): string
    {
        return $this->planId;
    }
}
