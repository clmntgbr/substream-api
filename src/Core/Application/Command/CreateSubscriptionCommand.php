<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;

final class CreateSubscriptionCommand extends CommandAbstract implements SyncCommandInterface
{
    public function __construct(
        private User $user,
        private string $planReference,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPlanReference(): string
    {
        return $this->planReference;
    }
}
