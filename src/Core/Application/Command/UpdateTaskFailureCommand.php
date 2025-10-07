<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\CommandAbstract;
use Symfony\Component\Uid\Uuid;

final class UpdateTaskFailureCommand extends CommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $taskId,
    ) {
    }

    public function getTaskId(): Uuid
    {
        return $this->taskId;
    }
}
