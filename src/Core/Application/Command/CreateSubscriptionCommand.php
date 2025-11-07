<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Uid\Uuid;

final class CreateSubscriptionCommand extends CommandAbstract implements SyncCommandInterface
{
    public function __construct(
        private Uuid $userId,
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
}
