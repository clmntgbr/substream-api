<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\SynchronousInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateSubscriptionCommand implements SynchronousInterface
{
    public function __construct(
        #[Assert\NotBlank]
        private Uuid $userId,
        #[Assert\NotBlank]
        private Uuid $planId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getPlanId(): Uuid
    {
        return $this->planId;
    }

    public function getStamps(): array
    {
        return [];
    }
}
