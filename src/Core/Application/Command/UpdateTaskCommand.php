<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandAbstract;
use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateTaskCommand extends AsyncCommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $taskId,
        private float $processingTime,
    ) {
    }

    public function getTaskId(): Uuid
    {
        return $this->taskId;
    }

    public function getProcessingTime(): int
    {
        return $this->processingTime;
    }
}
