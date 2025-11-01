<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\CommandAbstract;
use Symfony\Component\Uid\Uuid;

final class UpdateTaskSuccessCommand extends CommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $taskId,
        private int $processingTime,
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
